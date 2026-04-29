<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAbastecimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbastecimentoAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $tipo = $request->get('tipo');
        $busca = trim((string) $request->get('busca'));
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');

        $query = SolicitacaoAbastecimento::with([
            'usuario.cargo',
            'veiculo',
            'aprovador',
        ])->orderByDesc('created_at');

        if ($status && in_array($status, ['pendente', 'aprovada', 'reprovada', 'ajustada'])) {
            $query->where('status', $status);
        }

        if ($tipo && in_array($tipo, ['valor', 'litros'])) {
            $query->where('tipo_solicitacao', $tipo);
        }

        if ($busca !== '') {
            $query->where(function ($q) use ($busca) {
                $q->whereHas('usuario', function ($sub) use ($busca) {
                    $sub->where('name', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%");
                })->orWhereHas('veiculo', function ($sub) use ($busca) {
                    $sub->where('placa', 'like', "%{$busca}%")
                        ->orWhere('marca', 'like', "%{$busca}%")
                        ->orWhere('modelo', 'like', "%{$busca}%");
                });
            });
        }

        if (!empty($dataInicio)) {
            $query->whereDate('data_solicitacao', '>=', $dataInicio);
        }

        if (!empty($dataFim)) {
            $query->whereDate('data_solicitacao', '<=', $dataFim);
        }

        $solicitacoes = $query->get();

        return view('abastecimento.admin.index', compact(
            'solicitacoes',
            'status',
            'tipo',
            'busca',
            'dataInicio',
            'dataFim'
        ));
    }

    public function aprovar(Request $request, SolicitacaoAbastecimento $solicitacao)
    {
        if ($solicitacao->status !== 'pendente') {
            return back()->with('error', 'Somente solicitações pendentes podem ser aprovadas.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'quantidade_aprovada' => ['required', 'numeric', 'min:0.01'],
                'observacao_admin' => ['nullable', 'string'],
            ],
            [
                'quantidade_aprovada.required' => 'Informe a quantidade aprovada.',
                'quantidade_aprovada.numeric' => 'A quantidade aprovada deve ser numérica.',
                'quantidade_aprovada.min' => 'A quantidade aprovada deve ser maior que zero.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', [
                    'id' => $solicitacao->id,
                    'acao' => 'aprovar',
                    'rota' => route('abastecimento.admin.aprovar', $solicitacao),
                    'tipo' => $solicitacao->tipo_solicitacao,
                    'quantidade' => (string) $solicitacao->quantidade_solicitada,
                ]);
        }

        $solicitacao->update([
            'status' => 'aprovada',
            'quantidade_aprovada' => $request->quantidade_aprovada,
            'observacao_admin' => $request->observacao_admin,
            'aprovado_por' => auth()->id(),
            'aprovado_em' => now(),
        ]);

        return back()->with('success', 'Solicitação aprovada com sucesso.');
    }

    public function reprovar(Request $request, SolicitacaoAbastecimento $solicitacao)
    {
        if ($solicitacao->status !== 'pendente') {
            return back()->with('error', 'Somente solicitações pendentes podem ser reprovadas.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'observacao_admin' => ['required', 'string'],
            ],
            [
                'observacao_admin.required' => 'Informe o motivo da reprovação.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', [
                    'id' => $solicitacao->id,
                    'acao' => 'reprovar',
                    'rota' => route('abastecimento.admin.reprovar', $solicitacao),
                    'tipo' => $solicitacao->tipo_solicitacao,
                    'quantidade' => (string) $solicitacao->quantidade_solicitada,
                ]);
        }

        $solicitacao->update([
            'status' => 'reprovada',
            'quantidade_aprovada' => null,
            'observacao_admin' => $request->observacao_admin,
            'aprovado_por' => auth()->id(),
            'aprovado_em' => now(),
        ]);

        return back()->with('success', 'Solicitação reprovada com sucesso.');
    }

    public function ajustar(Request $request, SolicitacaoAbastecimento $solicitacao)
    {
        if ($solicitacao->status !== 'pendente') {
            return back()->with('error', 'Somente solicitações pendentes podem ser ajustadas.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'quantidade_aprovada' => ['required', 'numeric', 'min:0.01'],
                'observacao_admin' => ['required', 'string'],
            ],
            [
                'quantidade_aprovada.required' => 'Informe a quantidade ajustada.',
                'quantidade_aprovada.numeric' => 'A quantidade ajustada deve ser numérica.',
                'quantidade_aprovada.min' => 'A quantidade ajustada deve ser maior que zero.',
                'observacao_admin.required' => 'Informe a justificativa do ajuste.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', [
                    'id' => $solicitacao->id,
                    'acao' => 'ajustar',
                    'rota' => route('abastecimento.admin.ajustar', $solicitacao),
                    'tipo' => $solicitacao->tipo_solicitacao,
                    'quantidade' => (string) $solicitacao->quantidade_solicitada,
                ]);
        }

        $solicitacao->update([
            'status' => 'ajustada',
            'quantidade_aprovada' => $request->quantidade_aprovada,
            'observacao_admin' => $request->observacao_admin,
            'aprovado_por' => auth()->id(),
            'aprovado_em' => now(),
        ]);

        return back()->with('success', 'Solicitação ajustada com sucesso.');
    }
}