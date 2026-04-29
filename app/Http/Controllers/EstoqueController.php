<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\MovimentacaoEstoque;
use App\Models\Obra;
use App\Models\Produto;
use App\Services\EstoqueSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EstoqueController extends Controller
{
    public function index(Request $request)
    {
        app(EstoqueSyncService::class)->syncAll();

        $obraId = $request->get('obra_id');

        $obras = Obra::where('status', 'ativa')
            ->orderBy('nome')
            ->get();

        // SOMENTE PARA O MODAL DE MOVIMENTAÇÃO
        $produtosMovimentacao = Produto::with([
            'variacoes' => function ($query) {
                $query->where('status', 'ativo')
                    ->orderBy('nome_variacao');
            }
        ])
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        // TABELA PRINCIPAL DO ESTOQUE
// mostra ativos e inativos, tanto no central quanto na obra filtrada
$estoqueTabelaQuery = Estoque::select(
        'estoques.produto_id',
        'estoques.produto_variacao_id',
        DB::raw('SUM(estoques.quantidade_atual) as quantidade_total')
    )
    ->with(['produto', 'variacao'])
    ->join('produtos', 'estoques.produto_id', '=', 'produtos.id')
    ->leftJoin('produto_variacoes', 'estoques.produto_variacao_id', '=', 'produto_variacoes.id')
    ->where(function ($query) {
        $query->where('produtos.controla_variacao', false)
              ->orWhereNotNull('estoques.produto_variacao_id');
    });

if ($obraId) {
    $estoqueTabelaQuery->where('estoques.obra_id', $obraId);
} else {
    $estoqueTabelaQuery->whereNull('estoques.obra_id');
}

$estoqueTabela = $estoqueTabelaQuery
    ->groupBy(
        'estoques.produto_id',
        'estoques.produto_variacao_id',
        'produtos.nome',
        'produtos.controla_variacao',
        'produto_variacoes.nome_variacao',
        'produto_variacoes.cor',
        'produto_variacoes.tamanho'
    )
    ->orderBy('produtos.nome')
    ->orderBy('produto_variacoes.nome_variacao')
    ->orderBy('produto_variacoes.cor')
    ->orderBy('produto_variacoes.tamanho')
    ->get();

        // MAPA DO ESTOQUE CENTRAL
        $estoquesCentrais = Estoque::whereNull('obra_id')
            ->with(['produto', 'variacao'])
            ->get()
            ->mapWithKeys(function ($estoque) {
                $chave = $estoque->produto_id . '-' . ($estoque->produto_variacao_id ?? 'null');

                return [$chave => $estoque];
            });

        return view('estoque.index', compact(
            'obras',
            'produtosMovimentacao',
            'estoqueTabela',
            'estoquesCentrais',
            'obraId'
        ));
    }

    public function reabastecer(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'obra_id' => ['nullable', 'exists:obras,id'],
                'obra_origem_id' => ['nullable', 'exists:obras,id'],
                'obra_destino_id' => ['nullable', 'exists:obras,id'],
                'tipo_movimentacao' => ['required', Rule::in([
                    'entrada',
                    'ajuste',
                    'transferencia',
                    'transferencia_entre_obras'
                ])],
                'data_movimentacao' => ['required', 'date'],
                'itens' => ['required', 'array'],
                'itens.*.produto_id' => ['required', 'exists:produtos,id'],
                'itens.*.produto_variacao_id' => ['nullable', 'exists:produto_variacoes,id'],
                'itens.*.quantidade' => ['nullable', 'integer', 'min:0'],
            ],
            [
                'obra_id.exists' => 'A obra selecionada é inválida.',
                'obra_origem_id.exists' => 'A obra de origem é inválida.',
                'obra_destino_id.exists' => 'A obra de destino é inválida.',
                'tipo_movimentacao.required' => 'Selecione o tipo de movimentação.',
                'tipo_movimentacao.in' => 'O tipo de movimentação é inválido.',
                'data_movimentacao.required' => 'Informe a data da movimentação.',
                'data_movimentacao.date' => 'Informe uma data válida.',
                'itens.required' => 'Nenhum item foi enviado.',
                'itens.*.produto_id.required' => 'Um dos produtos enviados é inválido.',
                'itens.*.produto_id.exists' => 'Um dos produtos enviados não existe.',
                'itens.*.produto_variacao_id.exists' => 'Uma das variações enviadas não existe.',
                'itens.*.quantidade.integer' => 'A quantidade deve ser um número inteiro.',
                'itens.*.quantidade.min' => 'A quantidade não pode ser negativa.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('estoque.index', ['obra_id' => $request->obra_id])
                ->withErrors($validator)
                ->withInput()
                ->with('open_reabastecer_modal', true);
        }

        $itensInformados = collect($request->itens)
            ->filter(function ($item) {
                return isset($item['quantidade'])
                    && $item['quantidade'] !== null
                    && $item['quantidade'] !== ''
                    && (int) $item['quantidade'] > 0;
            })
            ->values();

        if ($itensInformados->isEmpty()) {
            return redirect()
                ->route('estoque.index', ['obra_id' => $request->obra_id])
                ->withErrors([
                    'itens' => 'Informe ao menos uma quantidade maior que zero.',
                ])
                ->withInput()
                ->with('open_reabastecer_modal', true);
        }

        if ($request->tipo_movimentacao === 'transferencia' && !$request->obra_id) {
            return redirect()
                ->route('estoque.index')
                ->withErrors([
                    'obra_id' => 'Selecione a obra de destino para transferir do estoque central.',
                ])
                ->withInput()
                ->with('open_reabastecer_modal', true);
        }

        if (
            in_array($request->tipo_movimentacao, ['entrada', 'ajuste']) &&
            ($request->obra_id || $request->obra_origem_id || $request->obra_destino_id)
        ) {
            return redirect()
                ->route('estoque.index', ['obra_id' => $request->obra_id])
                ->withErrors([
                    'tipo_movimentacao' => 'Entrada e ajuste servem apenas para o estoque central.',
                ])
                ->withInput()
                ->with('open_reabastecer_modal', true);
        }

        if ($request->tipo_movimentacao === 'transferencia_entre_obras') {
            if (!$request->obra_origem_id || !$request->obra_destino_id) {
                return redirect()
                    ->route('estoque.index')
                    ->withErrors([
                        'obra_origem_id' => 'Selecione a obra de origem e a obra de destino.',
                    ])
                    ->withInput()
                    ->with('open_reabastecer_modal', true);
            }

            if ((int) $request->obra_origem_id === (int) $request->obra_destino_id) {
                return redirect()
                    ->route('estoque.index')
                    ->withErrors([
                        'obra_destino_id' => 'A obra de origem e a obra de destino devem ser diferentes.',
                    ])
                    ->withInput()
                    ->with('open_reabastecer_modal', true);
            }
        }

        try {
            DB::transaction(function () use ($request, $itensInformados) {
                foreach ($itensInformados as $item) {
                    $produtoVariacaoId = !empty($item['produto_variacao_id'])
                        ? $item['produto_variacao_id']
                        : null;

                    $quantidadeInformada = (int) $item['quantidade'];

                    // ENTRADA NO CENTRAL
                    if ($request->tipo_movimentacao === 'entrada') {
                        $estoqueCentral = Estoque::whereNull('obra_id')
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn ($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn ($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueCentral) {
                            $estoqueCentral = Estoque::create([
                                'obra_id' => null,
                                'produto_id' => $item['produto_id'],
                                'produto_variacao_id' => $produtoVariacaoId,
                                'quantidade_atual' => 0,
                            ]);
                        }

                        $quantidadeAnterior = (int) $estoqueCentral->quantidade_atual;
                        $quantidadePosterior = $quantidadeAnterior + $quantidadeInformada;

                        $estoqueCentral->update([
                            'quantidade_atual' => $quantidadePosterior,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => null,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'entrada',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $quantidadeAnterior,
                            'quantidade_posterior' => $quantidadePosterior,
                            'observacao' => 'Entrada no estoque central.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);
                    }

                    // AJUSTE NO CENTRAL
                    elseif ($request->tipo_movimentacao === 'ajuste') {
                        $estoqueCentral = Estoque::whereNull('obra_id')
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn ($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn ($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueCentral) {
                            $estoqueCentral = Estoque::create([
                                'obra_id' => null,
                                'produto_id' => $item['produto_id'],
                                'produto_variacao_id' => $produtoVariacaoId,
                                'quantidade_atual' => 0,
                            ]);
                        }

                        $quantidadeAnterior = (int) $estoqueCentral->quantidade_atual;
                        $quantidadePosterior = $quantidadeInformada;

                        $estoqueCentral->update([
                            'quantidade_atual' => $quantidadePosterior,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => null,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'ajuste',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $quantidadeAnterior,
                            'quantidade_posterior' => $quantidadePosterior,
                            'observacao' => 'Ajuste manual no estoque central.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);
                    }

                    // TRANSFERÊNCIA DO CENTRAL PARA OBRA
                                        elseif ($request->tipo_movimentacao === 'transferencia') {
                        $estoqueCentral = Estoque::whereNull('obra_id')
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueCentral || (int) $estoqueCentral->quantidade_atual < $quantidadeInformada) {
                            $produto = Produto::find($item['produto_id']);
                            $nomeProduto = $produto?->nome ?? 'Produto';
                            throw new \RuntimeException("Estoque central insuficiente para o item: {$nomeProduto}.");
                        }

                        $estoqueObra = Estoque::where('obra_id', $request->obra_id)
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueObra) {
                            $estoqueObra = Estoque::create([
                                'obra_id' => $request->obra_id,
                                'produto_id' => $item['produto_id'],
                                'produto_variacao_id' => $produtoVariacaoId,
                                'quantidade_atual' => 0,
                            ]);

                            $estoqueObra = Estoque::where('id', $estoqueObra->id)->lockForUpdate()->first();
                        }

                        $centralAnterior = (int) $estoqueCentral->quantidade_atual;
                        $centralPosterior = $centralAnterior - $quantidadeInformada;

                        $obraAnterior = (int) $estoqueObra->quantidade_atual;
                        $obraPosterior = $obraAnterior + $quantidadeInformada;

                        $estoqueCentral->update([
                            'quantidade_atual' => $centralPosterior,
                        ]);

                        $estoqueObra->update([
                            'quantidade_atual' => $obraPosterior,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => null,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'ajuste',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $centralAnterior,
                            'quantidade_posterior' => $centralPosterior,
                            'observacao' => 'Saída do estoque central para a obra ID ' . $request->obra_id . '.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => $request->obra_id,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'entrada',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $obraAnterior,
                            'quantidade_posterior' => $obraPosterior,
                            'observacao' => 'Recebido do estoque central.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);
                    }

                    // TRANSFERÊNCIA ENTRE OBRAS
                    else {
                        $estoqueOrigem = Estoque::where('obra_id', $request->obra_origem_id)
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueOrigem || (int) $estoqueOrigem->quantidade_atual < $quantidadeInformada) {
                            $produto = Produto::find($item['produto_id']);
                            $nomeProduto = $produto?->nome ?? 'Produto';
                            throw new \RuntimeException("Estoque insuficiente na obra de origem para o item: {$nomeProduto}.");
                        }

                        $estoqueDestino = Estoque::where('obra_id', $request->obra_destino_id)
                            ->where('produto_id', $item['produto_id'])
                            ->when(
                                $produtoVariacaoId,
                                fn($query) => $query->where('produto_variacao_id', $produtoVariacaoId),
                                fn($query) => $query->whereNull('produto_variacao_id')
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$estoqueDestino) {
                            $estoqueDestino = Estoque::create([
                                'obra_id' => $request->obra_destino_id,
                                'produto_id' => $item['produto_id'],
                                'produto_variacao_id' => $produtoVariacaoId,
                                'quantidade_atual' => 0,
                            ]);

                            $estoqueDestino = Estoque::where('id', $estoqueDestino->id)->lockForUpdate()->first();
                        }

                        $origemAnterior = (int) $estoqueOrigem->quantidade_atual;
                        $origemPosterior = $origemAnterior - $quantidadeInformada;

                        $destinoAnterior = (int) $estoqueDestino->quantidade_atual;
                        $destinoPosterior = $destinoAnterior + $quantidadeInformada;

                        $estoqueOrigem->update([
                            'quantidade_atual' => $origemPosterior,
                        ]);

                        $estoqueDestino->update([
                            'quantidade_atual' => $destinoPosterior,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => $request->obra_origem_id,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'ajuste',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $origemAnterior,
                            'quantidade_posterior' => $origemPosterior,
                            'observacao' => 'Transferência para a obra ID ' . $request->obra_destino_id . '.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);

                        MovimentacaoEstoque::create([
                            'obra_id' => $request->obra_destino_id,
                            'produto_id' => $item['produto_id'],
                            'produto_variacao_id' => $produtoVariacaoId,
                            'tipo_movimentacao' => 'entrada',
                            'quantidade' => $quantidadeInformada,
                            'quantidade_anterior' => $destinoAnterior,
                            'quantidade_posterior' => $destinoPosterior,
                            'observacao' => 'Recebido da obra ID ' . $request->obra_origem_id . '.',
                            'user_id' => auth()->id(),
                            'data_movimentacao' => $request->data_movimentacao,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('estoque.index', ['obra_id' => $request->obra_id])
                ->withErrors([
                    'estoque' => $e->getMessage(),
                ])
                ->withInput()
                ->with('open_reabastecer_modal', true);
        }

        return redirect()
            ->route('estoque.index', ['obra_id' => $request->obra_id])
            ->with('success', 'Movimentação de estoque registrada com sucesso.');
    }
}