<?php

namespace App\Http\Controllers;

use App\Models\EntregaEpi;
use App\Models\EntregaEpiComprovante;
use App\Models\EntregaEpiDevolucao;
use App\Models\EntregaEpiDevolucaoItem;
use App\Models\EntregaEpiItem;
use App\Models\Estoque;
use App\Models\Funcionario;
use App\Models\MovimentacaoEstoque;
use App\Models\Obra;
use App\Models\Produto;
use App\Services\EstoqueSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EntregaEpiController extends Controller
{
    public function index(Request $request)
    {
        app(EstoqueSyncService::class)->syncAll();

        $search = trim((string) $request->get('search', ''));
        $obraId = $request->get('obra_id');

        $funcionarios = Funcionario::with([
            'obra',
            'cargo',
            'entregasEpi' => function ($query) {
                $query->with([
                    'itens.produto',
                    'itens.variacao',
                    'comprovantes',
                    'devolucoes.itens.produto',
                    'devolucoes.itens.variacao',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            },
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nome', 'like', '%' . $search . '%')
                        ->orWhere('matricula', 'like', '%' . $search . '%')
                        ->orWhere('cpf', 'like', '%' . $search . '%');
                });
            })
            ->when($obraId, function ($query) use ($obraId) {
                $query->where('obra_id', $obraId);
            })
            ->orderBy('nome')
            ->get()
            ->map(function ($funcionario) {
                $ultimaEntrega = $funcionario->entregasEpi->first();

                $funcionario->ultima_entrega = $ultimaEntrega;
                $funcionario->ultimo_comprovante = $ultimaEntrega?->comprovantes?->last();
                $funcionario->itens_pendentes_devolucao = $this->calcularItensPendentesDevolucao($funcionario);

                return $funcionario;
            });

        $obras = Obra::orderBy('nome')->get();

        $funcionariosModal = Funcionario::with([
            'obra',
            'cargo',
            'entregasEpi' => function ($query) {
                $query->with([
                    'itens.produto',
                    'itens.variacao',
                    'devolucoes.itens.produto',
                    'devolucoes.itens.variacao',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            }
        ])
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get()
            ->map(function ($funcionario) {
                $funcionario->itens_pendentes_devolucao = $this->calcularItensPendentesDevolucao($funcionario);
                return $funcionario;
            });

        $obrasAtivas = Obra::where('status', 'ativa')
            ->orderBy('nome')
            ->get();

        $produtos = Produto::with([
            'variacoes' => function ($query) {
                $query->where('status', 'ativo')
                    ->orderBy('nome_variacao');
            }
        ])
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        $estoqueMapa = Estoque::get()
            ->mapWithKeys(function ($estoque) {
                $chave = $estoque->obra_id . '-' . $estoque->produto_id . '-' . ($estoque->produto_variacao_id ?? 'null');
                return [$chave => (int) $estoque->quantidade_atual];
            });

        return view('entregas.index', compact(
            'funcionarios',
            'obras',
            'search',
            'obraId',
            'funcionariosModal',
            'obrasAtivas',
            'produtos',
            'estoqueMapa'
        ));
    }

    public function historico($funcionarioId)
    {
        $funcionario = Funcionario::with([
            'obra.ultimoTreinamentoDds',
            'cargo',
            'entregasEpi' => function ($query) {
                $query->with([
                    'usuario',
                    'itens.produto',
                    'itens.variacao',
                    'comprovantes',
                    'devolucoes.usuario',
                    'devolucoes.itens.produto',
                    'devolucoes.itens.variacao',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            },
        ])->findOrFail($funcionarioId);

        $ultimaEntrega = $funcionario->entregasEpi->first();

        return view('entregas.historico', compact('funcionario', 'ultimaEntrega'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obra_id' => ['required', 'exists:obras,id'],
            'funcionario_id' => ['required', 'exists:funcionarios,id'],
            'data_entrega' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
            'itens' => ['required', 'array'],
            'motivo_devolucao' => ['nullable', 'string'],
            'devolucoes' => ['nullable', 'array'],
        ], [
            'obra_id.required' => 'Selecione a obra.',
            'obra_id.exists' => 'A obra selecionada é inválida.',
            'funcionario_id.required' => 'Selecione o funcionário.',
            'funcionario_id.exists' => 'O funcionário selecionado é inválido.',
            'data_entrega.required' => 'Informe a data da entrega.',
            'data_entrega.date' => 'Informe uma data válida.',
            'itens.required' => 'Nenhum item foi enviado.',
        ]);

        $funcionario = Funcionario::with([
            'entregasEpi' => function ($query) {
                $query->with([
                    'itens',
                    'devolucoes.itens',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            }
        ])->findOrFail($request->funcionario_id);

        if ((int) $funcionario->obra_id !== (int) $request->obra_id) {
            return redirect()
                ->route('entregas.index')
                ->withErrors([
                    'funcionario_id' => 'O funcionário selecionado não pertence à obra informada.',
                ])
                ->withInput()
                ->with('open_create_modal', true);
        }

        $itensInformados = collect($request->itens)
            ->filter(function ($item) {
                return isset($item['quantidade']) && (int) $item['quantidade'] > 0;
            })
            ->values();

        if ($itensInformados->isEmpty()) {
            return redirect()
                ->route('entregas.index')
                ->withErrors([
                    'itens' => 'Informe ao menos uma quantidade maior que zero.',
                ])
                ->withInput()
                ->with('open_create_modal', true);
        }

        foreach ($itensInformados as $item) {
            if (empty($item['produto_id'])) {
                return redirect()
                    ->route('entregas.index')
                    ->withErrors([
                        'itens' => 'Um dos produtos informados é inválido.',
                    ])
                    ->withInput()
                    ->with('open_create_modal', true);
            }

            $produtoVariacaoId = !empty($item['produto_variacao_id']) ? $item['produto_variacao_id'] : null;

            $estoque = Estoque::where('obra_id', $request->obra_id)
                ->where('produto_id', $item['produto_id'])
                ->when($produtoVariacaoId, function ($query) use ($produtoVariacaoId) {
                    $query->where('produto_variacao_id', $produtoVariacaoId);
                }, function ($query) {
                    $query->whereNull('produto_variacao_id');
                })
                ->first();

            if (!$estoque) {
                return redirect()
                    ->route('entregas.index')
                    ->withErrors([
                        'itens' => 'Não existe estoque base cadastrado para um dos itens na obra selecionada.',
                    ])
                    ->withInput()
                    ->with('open_create_modal', true);
            }

            if ((int) $estoque->quantidade_atual < (int) $item['quantidade']) {
                return redirect()
                    ->route('entregas.index')
                    ->withErrors([
                        'itens' => 'Estoque insuficiente para um dos itens informados.',
                    ])
                    ->withInput()
                    ->with('open_create_modal', true);
            }
        }

        $pendentesMapa = $this->calcularPendentesMapa($funcionario);

        $devolucoesInformadas = collect($request->devolucoes ?? [])
            ->filter(function ($item) {
                return isset($item['quantidade']) && (int) $item['quantidade'] > 0;
            })
            ->values();

        foreach ($devolucoesInformadas as $devolucao) {
            $produtoId = (int) ($devolucao['produto_id'] ?? 0);
            $produtoVariacaoId = !empty($devolucao['produto_variacao_id']) ? (int) $devolucao['produto_variacao_id'] : null;
            $quantidadeDevolvida = (int) ($devolucao['quantidade'] ?? 0);

            $chave = $produtoId . '-' . ($produtoVariacaoId ?? 'null');
            $pendente = $pendentesMapa[$chave] ?? null;

            if (!$pendente) {
                return redirect()
                    ->route('entregas.index')
                    ->withErrors([
                        'devolucoes' => 'Um dos itens devolvidos não está pendente para este funcionário.',
                    ])
                    ->withInput()
                    ->with('open_create_modal', true);
            }

            if ($quantidadeDevolvida > (int) $pendente['quantidade_pendente']) {
                return redirect()
                    ->route('entregas.index')
                    ->withErrors([
                        'devolucoes' => 'A quantidade devolvida de um dos itens é maior que a quantidade pendente.',
                    ])
                    ->withInput()
                    ->with('open_create_modal', true);
            }
        }

        DB::transaction(function () use ($request, $itensInformados, $devolucoesInformadas, $pendentesMapa) {
            $entrega = EntregaEpi::create([
                'funcionario_id' => $request->funcionario_id,
                'obra_id' => $request->obra_id,
                'user_id' => auth()->id(),
                'data_entrega' => $request->data_entrega,
                'status_comprovante' => 'pendente',
                'observacoes' => $request->observacoes,
            ]);

            if ($devolucoesInformadas->isNotEmpty()) {
                $devolucao = EntregaEpiDevolucao::create([
                    'entrega_epi_id' => $entrega->id,
                    'entrega_origem_id' => null,
                    'funcionario_id' => $request->funcionario_id,
                    'obra_id' => $request->obra_id,
                    'user_id' => auth()->id(),
                    'data_devolucao' => $request->data_entrega,
                    'motivo' => $request->motivo_devolucao,
                ]);

                foreach ($devolucoesInformadas as $item) {
                    $produtoId = (int) $item['produto_id'];
                    $produtoVariacaoId = !empty($item['produto_variacao_id']) ? (int) $item['produto_variacao_id'] : null;
                    $quantidadeDevolvida = (int) $item['quantidade'];

                    EntregaEpiDevolucaoItem::create([
                        'entrega_epi_devolucao_id' => $devolucao->id,
                        'produto_id' => $produtoId,
                        'produto_variacao_id' => $produtoVariacaoId,
                        'quantidade' => $quantidadeDevolvida,
                    ]);

                    $estoque = Estoque::where('obra_id', $request->obra_id)
                        ->where('produto_id', $produtoId)
                        ->when($produtoVariacaoId, function ($query) use ($produtoVariacaoId) {
                            $query->where('produto_variacao_id', $produtoVariacaoId);
                        }, function ($query) {
                            $query->whereNull('produto_variacao_id');
                        })
                        ->lockForUpdate()
                        ->first();

                    if (!$estoque) {
                        $estoque = Estoque::create([
                            'obra_id' => $request->obra_id,
                            'produto_id' => $produtoId,
                            'produto_variacao_id' => $produtoVariacaoId,
                            'quantidade_atual' => 0,
                        ]);
                    }

                    $quantidadeAnterior = (int) $estoque->quantidade_atual;
                    $quantidadePosterior = $quantidadeAnterior + $quantidadeDevolvida;

                    $estoque->update([
                        'quantidade_atual' => $quantidadePosterior,
                    ]);

                    MovimentacaoEstoque::create([
                        'obra_id' => $request->obra_id,
                        'produto_id' => $produtoId,
                        'produto_variacao_id' => $produtoVariacaoId,
                        'tipo_movimentacao' => 'ajuste',
                        'quantidade' => $quantidadeDevolvida,
                        'quantidade_anterior' => $quantidadeAnterior,
                        'quantidade_posterior' => $quantidadePosterior,
                        'observacao' => 'Devolução de EPI por funcionário. Motivo: ' . ($request->motivo_devolucao ?: 'Não informado'),
                        'user_id' => auth()->id(),
                        'data_movimentacao' => $request->data_entrega,
                    ]);
                }
            }

            foreach ($itensInformados as $item) {
                $produtoVariacaoId = !empty($item['produto_variacao_id']) ? $item['produto_variacao_id'] : null;

                $estoque = Estoque::where('obra_id', $request->obra_id)
                    ->where('produto_id', $item['produto_id'])
                    ->when($produtoVariacaoId, function ($query) use ($produtoVariacaoId) {
                        $query->where('produto_variacao_id', $produtoVariacaoId);
                    }, function ($query) {
                        $query->whereNull('produto_variacao_id');
                    })
                    ->lockForUpdate()
                    ->first();

                $quantidadeAnterior = (int) $estoque->quantidade_atual;
                $quantidadeEntrega = (int) $item['quantidade'];
                $quantidadePosterior = $quantidadeAnterior - $quantidadeEntrega;

                EntregaEpiItem::create([
                    'entrega_epi_id' => $entrega->id,
                    'produto_id' => $item['produto_id'],
                    'produto_variacao_id' => $produtoVariacaoId,
                    'quantidade' => $quantidadeEntrega,
                ]);

                $estoque->update([
                    'quantidade_atual' => $quantidadePosterior,
                ]);

                MovimentacaoEstoque::create([
                    'obra_id' => $request->obra_id,
                    'produto_id' => $item['produto_id'],
                    'produto_variacao_id' => $produtoVariacaoId,
                    'tipo_movimentacao' => 'ajuste',
                    'quantidade' => $quantidadeEntrega,
                    'quantidade_anterior' => $quantidadeAnterior,
                    'quantidade_posterior' => $quantidadePosterior,
                    'observacao' => 'Entrega de EPI para funcionário.',
                    'user_id' => auth()->id(),
                    'data_movimentacao' => $request->data_entrega,
                ]);
            }
        });

        return redirect()
            ->route('entregas.index')
            ->with('success', 'Entrega registrada com sucesso.');
    }

    public function destroy(EntregaEpi $entrega)
    {
        $entrega->load('itens');

        DB::transaction(function () use ($entrega) {
            foreach ($entrega->itens as $item) {
                $estoque = Estoque::where('obra_id', $entrega->obra_id)
                    ->where('produto_id', $item->produto_id)
                    ->when($item->produto_variacao_id, function ($query) use ($item) {
                        $query->where('produto_variacao_id', $item->produto_variacao_id);
                    }, function ($query) {
                        $query->whereNull('produto_variacao_id');
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$estoque) {
                    throw new \RuntimeException('Estoque base não encontrado para devolver item excluído.');
                }

                $quantidadeAnterior = (int) $estoque->quantidade_atual;
                $quantidadePosterior = $quantidadeAnterior + (int) $item->quantidade;

                $estoque->update([
                    'quantidade_atual' => $quantidadePosterior,
                ]);

                MovimentacaoEstoque::create([
                    'obra_id' => $entrega->obra_id,
                    'produto_id' => $item->produto_id,
                    'produto_variacao_id' => $item->produto_variacao_id,
                    'tipo_movimentacao' => 'ajuste',
                    'quantidade' => (int) $item->quantidade,
                    'quantidade_anterior' => $quantidadeAnterior,
                    'quantidade_posterior' => $quantidadePosterior,
                    'observacao' => 'Exclusão de entrega de EPI. Estoque devolvido.',
                    'user_id' => auth()->id(),
                    'data_movimentacao' => now()->toDateString(),
                ]);
            }

            $entrega->delete();
        });

        return redirect()
            ->route('entregas.index')
            ->with('success', 'Entrega excluída e estoque devolvido com sucesso.');
    }

    public function uploadComprovantes(Request $request, EntregaEpi $entrega)
    {
        $request->validate([
            'comprovantes' => ['nullable', 'array'],
            'comprovantes.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'foto_camera_base64' => ['nullable', 'string'],
            'foto_camera_nome' => ['nullable', 'string', 'max:255'],
            'foto_camera_mime' => ['nullable', 'string', 'max:100'],
        ], [
            'comprovantes.array' => 'Os comprovantes enviados são inválidos.',
            'comprovantes.*.file' => 'Um dos arquivos enviados é inválido.',
            'comprovantes.*.mimes' => 'Os comprovantes devem ser JPG, JPEG, PNG ou PDF.',
            'comprovantes.*.max' => 'Cada arquivo pode ter no máximo 10MB.',
        ]);

        $temArquivoNormal = $request->hasFile('comprovantes');
        $temFotoCamera = filled($request->foto_camera_base64);

        if (!$temArquivoNormal && !$temFotoCamera) {
            return redirect()
                ->route('epi.historico', $entrega->funcionario_id)
                ->with('error', 'Selecione um arquivo ou tire uma foto para enviar.');
        }

        DB::transaction(function () use ($request, $entrega, $temArquivoNormal, $temFotoCamera) {
            if ($temArquivoNormal) {
                foreach ($request->file('comprovantes', []) as $arquivo) {
                    if (!$arquivo) {
                        continue;
                    }

                    $caminho = $arquivo->store('comprovantes_epi', 'public');

                    $entrega->comprovantes()->create([
                        'arquivo' => $caminho,
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'mime_type' => $arquivo->getClientMimeType(),
                    ]);
                }
            }

            if ($temFotoCamera) {
                $base64 = $request->foto_camera_base64;
                $mimeRecebido = $request->foto_camera_mime ?: null;
                $nomeRecebido = $request->foto_camera_nome ?: null;

                if (!str_contains($base64, ',')) {
                    throw new \RuntimeException('Arquivo da câmera inválido.');
                }

                [$meta, $conteudo] = explode(',', $base64, 2);

                $mimeType = 'image/jpeg';
                $extensao = 'jpg';

                if (
                    str_contains($meta, 'application/pdf') ||
                    $mimeRecebido === 'application/pdf' ||
                    ($nomeRecebido && str_ends_with(strtolower($nomeRecebido), '.pdf'))
                ) {
                    $mimeType = 'application/pdf';
                    $extensao = 'pdf';
                } elseif (
                    str_contains($meta, 'image/png') ||
                    $mimeRecebido === 'image/png'
                ) {
                    $mimeType = 'image/png';
                    $extensao = 'png';
                } else {
                    $mimeType = 'image/jpeg';
                    $extensao = 'jpg';
                }

                $binario = base64_decode($conteudo, true);

                if ($binario === false) {
                    throw new \RuntimeException('Falha ao processar o arquivo capturado.');
                }

                $nomeArquivo = 'comprovante-camera-' . Str::uuid() . '.' . $extensao;
                $caminho = 'comprovantes_epi/' . $nomeArquivo;

                Storage::disk('public')->put($caminho, $binario);

                $entrega->comprovantes()->create([
                    'arquivo' => $caminho,
                    'nome_original' => $nomeRecebido ?: $nomeArquivo,
                    'mime_type' => $mimeType,
                ]);
            }

            $entrega->update([
                'status_comprovante' => 'anexado',
            ]);
        });

        return redirect()
            ->route('epi.historico', $entrega->funcionario_id)
            ->with('success', 'Comprovante(s) anexado(s) com sucesso.');
    }

    public function abrirComprovante(EntregaEpiComprovante $comprovante)
    {
        $caminho = storage_path('app/public/' . $comprovante->arquivo);

        if (!file_exists($caminho)) {
            abort(404);
        }

        return response()->file($caminho, [
            'Content-Type' => $comprovante->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . ($comprovante->nome_original ?: basename($caminho)) . '"',
        ]);
    }

    private function calcularItensPendentesDevolucao($funcionario): array
    {
        return array_values($this->calcularPendentesMapa($funcionario));
    }

    private function calcularPendentesMapa($funcionario): array
    {
        $mapa = [];

        $entregasOrdenadas = $funcionario->entregasEpi
            ->sortBy([
                ['data_entrega', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        foreach ($entregasOrdenadas as $entrega) {
            foreach ($entrega->itens as $item) {
                $produtoId = (int) $item->produto_id;
                $produtoVariacaoId = $item->produto_variacao_id ? (int) $item->produto_variacao_id : null;
                $chave = $produtoId . '-' . ($produtoVariacaoId ?? 'null');

                if (!isset($mapa[$chave])) {
                    $mapa[$chave] = [
                        'produto_id' => $produtoId,
                        'produto_variacao_id' => $produtoVariacaoId,
                        'produto_nome' => $item->produto->nome ?? '-',
                        'variacao_nome' => $item->variacao->nome_variacao ?? null,
                        'cor' => $item->variacao->cor ?? null,
                        'tamanho' => $item->variacao->tamanho ?? null,
                        'sku' => $item->variacao->sku ?? null,
                        'ca' => $item->variacao->ca ?? $item->produto->ca ?? null,
                        'quantidade_pendente' => 0,
                        'ultima_data_entrega' => null,
                    ];
                }

                $mapa[$chave]['quantidade_pendente'] += (int) $item->quantidade;
                $mapa[$chave]['ultima_data_entrega'] = $entrega->data_entrega
                    ? $entrega->data_entrega->format('d/m/Y')
                    : null;
            }

            foreach ($entrega->devolucoes as $devolucao) {
                foreach ($devolucao->itens as $itemDevolvido) {
                    $produtoId = (int) $itemDevolvido->produto_id;
                    $produtoVariacaoId = $itemDevolvido->produto_variacao_id ? (int) $itemDevolvido->produto_variacao_id : null;
                    $chave = $produtoId . '-' . ($produtoVariacaoId ?? 'null');

                    if (!isset($mapa[$chave])) {
                        continue;
                    }

                    $mapa[$chave]['quantidade_pendente'] -= (int) $itemDevolvido->quantidade;

                    if ($mapa[$chave]['quantidade_pendente'] <= 0) {
                        unset($mapa[$chave]);
                    }
                }
            }
        }

        return $mapa;
    }
}