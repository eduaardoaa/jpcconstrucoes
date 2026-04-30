<?php

namespace App\Http\Controllers;

use App\Models\DeslocamentoVeiculo;
use App\Models\DeslocamentoVeiculoEtapa;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class DeslocamentoVeiculoController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermissao('deslocamentos')) {
            abort(403);
        }

        $busca      = $request->busca;
        $status     = $request->status;
        $dataInicio = $request->data_inicio;
        $dataFim    = $request->data_fim;
        $usuarioId  = $request->usuario_id;

        $deslocamentos = DeslocamentoVeiculo::with([
            'usuario.cargo',
            'veiculo',
            'etapas',
            'saida',
            'chegada',
        ])
            ->when($busca, function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->whereHas('usuario', function ($uq) use ($busca) {
                        $uq->where('name', 'like', "%{$busca}%")
                            ->orWhere('email', 'like', "%{$busca}%");
                    })->orWhereHas('veiculo', function ($vq) use ($busca) {
                        $vq->where('placa', 'like', "%{$busca}%")
                            ->orWhere('marca', 'like', "%{$busca}%")
                            ->orWhere('modelo', 'like', "%{$busca}%");
                    });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dataInicio, function ($query) use ($dataInicio) {
                $query->whereDate('created_at', '>=', $dataInicio);
            })
            ->when($dataFim, function ($query) use ($dataFim) {
                $query->whereDate('created_at', '<=', $dataFim);
            })
            ->when($usuarioId, function ($query) use ($usuarioId) {
                $query->where('user_id', $usuarioId);
            })
            ->orderByDesc('created_at')
            ->get();

        $usuarios = \App\Models\User::whereIn(
            'id',
            DeslocamentoVeiculo::distinct()->pluck('user_id')
        )->orderBy('name')->get();

        return view('abastecimento.deslocamentos.controle', compact(
            'deslocamentos',
            'busca',
            'status',
            'dataInicio',
            'dataFim',
            'usuarioId',
            'usuarios'
        ));
    }

    public function meusDeslocamentos()
    {
        $user = auth()->user();

        $veiculos = Veiculo::where('status', 'ativo')
            ->orderBy('placa')
            ->get();

        $deslocamentos = DeslocamentoVeiculo::with([
            'veiculo',
            'etapas',
            'saida',
            'chegada',
        ])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $deslocamentoEmAndamento = $deslocamentos->firstWhere('status', 'em_andamento');

        return view('abastecimento.deslocamentos.meus', compact(
            'user',
            'veiculos',
            'deslocamentos',
            'deslocamentoEmAndamento'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make(
            $request->all(),
            [
                'veiculo_id'         => [
                    'required',
                    Rule::exists('veiculos', 'id')->where('status', 'ativo'),
                ],
                'motivo'            => ['nullable', 'string', 'max:255'],
                'observacao'        => ['nullable', 'string'],
                'data_saida'        => ['required', 'date'],
                'hora_saida'        => ['required'],
                'local_saida'       => ['required', 'string', 'max:255'],
                'latitude_saida'    => ['nullable', 'numeric'],
                'longitude_saida'   => ['nullable', 'numeric'],
                'km_saida'          => ['required', 'numeric', 'min:0'],
                'foto_saida_base64' => ['required', 'string'],
                'foto_saida_nome'   => ['nullable', 'string'],
                'foto_saida_mime'   => ['nullable', 'string'],
            ],
            [
                'veiculo_id.required'       => 'Selecione o veículo do deslocamento.',
                'veiculo_id.exists'         => 'O veículo selecionado é inválido ou está inativo.',
                'data_saida.required'       => 'A data de saída é obrigatória.',
                'hora_saida.required'       => 'A hora de saída é obrigatória.',
                'local_saida.required'      => 'O local de saída é obrigatório.',
                'km_saida.required'         => 'O KM de saída é obrigatório.',
                'foto_saida_base64.required'=> 'A foto do painel na saída é obrigatória.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('deslocamentos.meus')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal_saida', true);
        }

        $existeEmAndamento = DeslocamentoVeiculo::where('veiculo_id', $request->veiculo_id)
            ->where('status', 'em_andamento')
            ->exists();

        if ($existeEmAndamento) {
            return redirect()
                ->route('deslocamentos.meus')
                ->withInput()
                ->with('error', 'Já existe um deslocamento em andamento para este veículo.')
                ->with('open_modal_saida', true);
        }

        DB::transaction(function () use ($request, $user) {
            $fotoSaida = $this->salvarImagemBase64(
                $request->foto_saida_base64,
                'deslocamentos/saida',
                $request->foto_saida_nome,
                $request->foto_saida_mime
            );

            $deslocamento = DeslocamentoVeiculo::create([
                'user_id'    => $user->id,
                'veiculo_id' => $request->veiculo_id,
                'motivo'     => $request->motivo,
                'observacao' => $request->observacao,
                'status'     => 'em_andamento',
            ]);

            DeslocamentoVeiculoEtapa::create([
                'deslocamento_veiculo_id' => $deslocamento->id,
                'tipo_etapa'              => 'saida',
                'ordem'                   => 1,
                'data_etapa'              => $request->data_saida,
                'hora_etapa'              => $request->hora_saida,
                'local_etapa'             => $request->local_saida,
                'latitude'                => $request->latitude_saida,
                'longitude'               => $request->longitude_saida,
                'km_etapa'                => $request->km_saida,
                'foto_painel'             => $fotoSaida,
                'observacao'              => $request->observacao,
            ]);
        });

        return redirect()
            ->route('deslocamentos.meus')
            ->with('success', 'Saída registrada com sucesso.');
    }

    public function storeParada(Request $request, DeslocamentoVeiculo $deslocamento)
    {
        $user = auth()->user();

        if ((int) $deslocamento->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($deslocamento->status !== 'em_andamento') {
            return redirect()
                ->route('deslocamentos.meus')
                ->with('error', 'Esse deslocamento já foi finalizado.');
        }

        $ultimaEtapa = $deslocamento->etapas()->orderByDesc('ordem')->first();

        $validator = Validator::make(
            $request->all(),
            [
                'data_parada'        => ['required', 'date'],
                'hora_parada'        => ['required'],
                'local_parada'       => ['required', 'string', 'max:255'],
                'latitude_parada'    => ['nullable', 'numeric'],
                'longitude_parada'   => ['nullable', 'numeric'],
                'km_parada'          => ['required', 'numeric', 'min:0'],
                'foto_parada_base64' => ['required', 'string'],
                'foto_parada_nome'   => ['nullable', 'string'],
                'foto_parada_mime'   => ['nullable', 'string'],
                'observacao_parada'  => ['nullable', 'string'],
            ],
            [
                'data_parada.required'        => 'A data da parada é obrigatória.',
                'hora_parada.required'        => 'A hora da parada é obrigatória.',
                'local_parada.required'       => 'O local da parada é obrigatório.',
                'km_parada.required'          => 'O KM da parada é obrigatório.',
                'foto_parada_base64.required' => 'A foto do painel na parada é obrigatória.',
            ]
        );

        $validator->after(function ($validator) use ($request, $ultimaEtapa) {
            if ($ultimaEtapa && (float) $request->km_parada < (float) $ultimaEtapa->km_etapa) {
                $validator->errors()->add('km_parada', 'O KM da parada não pode ser menor que o KM da última etapa.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->route('deslocamentos.meus')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal_parada', $deslocamento->id);
        }

        $fotoParada = $this->salvarImagemBase64(
            $request->foto_parada_base64,
            'deslocamentos/paradas',
            $request->foto_parada_nome,
            $request->foto_parada_mime
        );

        DeslocamentoVeiculoEtapa::create([
            'deslocamento_veiculo_id' => $deslocamento->id,
            'tipo_etapa'              => 'parada',
            'ordem'                   => ($ultimaEtapa?->ordem ?? 0) + 1,
            'data_etapa'              => $request->data_parada,
            'hora_etapa'              => $request->hora_parada,
            'local_etapa'             => $request->local_parada,
            'latitude'                => $request->latitude_parada,
            'longitude'               => $request->longitude_parada,
            'km_etapa'                => $request->km_parada,
            'foto_painel'             => $fotoParada,
            'observacao'              => $request->observacao_parada,
        ]);

        return redirect()
            ->route('deslocamentos.meus')
            ->with('success', 'Parada registrada com sucesso.');
    }

    public function storeChegada(Request $request, DeslocamentoVeiculo $deslocamento)
    {
        $user = auth()->user();

        if ((int) $deslocamento->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($deslocamento->status !== 'em_andamento') {
            return redirect()
                ->route('deslocamentos.meus')
                ->with('error', 'Esse deslocamento já foi finalizado.');
        }

        $ultimaEtapa = $deslocamento->etapas()->orderByDesc('ordem')->first();

        $validator = Validator::make(
            $request->all(),
            [
                'data_chegada'        => ['required', 'date'],
                'hora_chegada'        => ['required'],
                'local_chegada'       => ['required', 'string', 'max:255'],
                'latitude_chegada'    => ['nullable', 'numeric'],
                'longitude_chegada'   => ['nullable', 'numeric'],
                'km_chegada'          => ['required', 'numeric', 'min:0'],
                'foto_chegada_base64' => ['required', 'string'],
                'foto_chegada_nome'   => ['nullable', 'string'],
                'foto_chegada_mime'   => ['nullable', 'string'],
                'observacao_chegada'  => ['nullable', 'string'],
            ],
            [
                'data_chegada.required'        => 'A data de chegada é obrigatória.',
                'hora_chegada.required'        => 'A hora de chegada é obrigatória.',
                'local_chegada.required'       => 'O local de chegada é obrigatório.',
                'km_chegada.required'          => 'O KM da chegada é obrigatório.',
                'foto_chegada_base64.required' => 'A foto do painel na chegada é obrigatória.',
            ]
        );

        $validator->after(function ($validator) use ($request, $ultimaEtapa) {
            if ($ultimaEtapa && (float) $request->km_chegada < (float) $ultimaEtapa->km_etapa) {
                $validator->errors()->add('km_chegada', 'O KM da chegada não pode ser menor que o KM da última etapa.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->route('deslocamentos.meus')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal_chegada', $deslocamento->id);
        }

        DB::transaction(function () use ($request, $deslocamento) {
            $ultimaEtapa = $deslocamento->etapas()->orderByDesc('ordem')->first();

            $fotoChegada = $this->salvarImagemBase64(
                $request->foto_chegada_base64,
                'deslocamentos/chegada',
                $request->foto_chegada_nome,
                $request->foto_chegada_mime
            );

            DeslocamentoVeiculoEtapa::create([
                'deslocamento_veiculo_id' => $deslocamento->id,
                'tipo_etapa'              => 'chegada',
                'ordem'                   => ($ultimaEtapa?->ordem ?? 0) + 1,
                'data_etapa'              => $request->data_chegada,
                'hora_etapa'              => $request->hora_chegada,
                'local_etapa'             => $request->local_chegada,
                'latitude'                => $request->latitude_chegada,
                'longitude'               => $request->longitude_chegada,
                'km_etapa'                => $request->km_chegada,
                'foto_painel'             => $fotoChegada,
                'observacao'              => $request->observacao_chegada,
            ]);

            $deslocamento->update(['status' => 'finalizado']);

            if ($deslocamento->veiculo) {
                $deslocamento->veiculo->update(['km_atual' => $request->km_chegada]);
            }
        });

        return redirect()
            ->route('deslocamentos.meus')
            ->with('success', 'Chegada registrada com sucesso.');
    }

    private function salvarImagemBase64(string $base64, string $pasta, ?string $nome = null, ?string $mime = null): string
    {
        if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,/', $base64, $matches)) {
            $mime = $matches[1];
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $base64 = str_replace(' ', '+', $base64);
        $arquivoBinario = base64_decode($base64);

        if ($arquivoBinario === false) {
            throw new \RuntimeException('Não foi possível decodificar a imagem enviada.');
        }

        $extensao = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $nomeArquivo = ($nome ? pathinfo($nome, PATHINFO_FILENAME) : Str::uuid()->toString())
            . '-' . time() . '.' . $extensao;

        $caminho = trim($pasta, '/') . '/' . $nomeArquivo;

        Storage::disk('public')->put($caminho, $arquivoBinario);

        return $caminho;
    }
}