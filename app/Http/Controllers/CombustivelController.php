<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAbastecimento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CombustivelController extends Controller
{
    public function index(Request $request)
    {
        $ano = (int) $request->get('ano', now()->year);
        $mes = $request->filled('mes') ? (int) $request->get('mes') : null;

        $query = SolicitacaoAbastecimento::with(['usuario', 'veiculo', 'aprovador'])
            ->whereYear('data_solicitacao', $ano)
            ->orderByDesc('data_solicitacao')
            ->orderByDesc('id');

        if ($mes && $mes >= 1 && $mes <= 12) {
            $query->whereMonth('data_solicitacao', $mes);
        }

        $solicitacoes = $query->get();

        $cards = [
            'total' => $solicitacoes->count(),
            'pendentes' => $solicitacoes->where('status', 'pendente')->count(),
            'aprovadas' => $solicitacoes->where('status', 'aprovada')->count(),
            'reprovadas' => $solicitacoes->where('status', 'reprovada')->count(),
            'ajustadas' => $solicitacoes->where('status', 'ajustada')->count(),

            'valor_aprovado' => $solicitacoes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'valor')
                ->sum(fn ($item) => (float) $item->quantidade_aprovada),

            'litros_aprovados' => $solicitacoes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'litros')
                ->sum(fn ($item) => (float) $item->quantidade_aprovada),
        ];

        $statusChart = [
            'labels' => ['Pendentes', 'Aprovadas', 'Reprovadas', 'Ajustadas'],
            'values' => [
                $cards['pendentes'],
                $cards['aprovadas'],
                $cards['reprovadas'],
                $cards['ajustadas'],
            ],
        ];

        $mesesLabels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        if ($mes) {
            $diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            $labelsPeriodo = [];
            $valoresPeriodo = [];

            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                $labelsPeriodo[] = str_pad($dia, 2, '0', STR_PAD_LEFT);
                $valoresPeriodo[] = $solicitacoes
                    ->filter(fn ($item) => optional($item->data_solicitacao)->day === $dia)
                    ->count();
            }

            $mesChart = [
                'labels' => $labelsPeriodo,
                'values' => $valoresPeriodo,
            ];
        } else {
            $valoresPorMes = [];

            for ($i = 1; $i <= 12; $i++) {
                $valoresPorMes[] = $solicitacoes
                    ->filter(fn ($item) => optional($item->data_solicitacao)->month === $i)
                    ->count();
            }

            $mesChart = [
                'labels' => $mesesLabels,
                'values' => $valoresPorMes,
            ];
        }

        $veiculosAgrupados = $solicitacoes
            ->filter(fn ($item) => $item->veiculo)
            ->groupBy(function ($item) {
                return trim(($item->veiculo->placa ?? '—') . ' - ' . ($item->veiculo->marca ?? '') . ' ' . ($item->veiculo->modelo ?? ''));
            })
            ->map(fn ($grupo) => $grupo->count())
            ->sortDesc()
            ->take(7);

        $veiculoChart = [
            'labels' => $veiculosAgrupados->keys()->values()->toArray(),
            'values' => $veiculosAgrupados->values()->toArray(),
        ];

        $anos = SolicitacaoAbastecimento::query()
            ->selectRaw('YEAR(data_solicitacao) as ano')
            ->whereNotNull('data_solicitacao')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano');

        if ($anos->isEmpty()) {
            $anos = collect([now()->year]);
        }

        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        return view('abastecimento.painel.index', compact(
            'solicitacoes',
            'cards',
            'statusChart',
            'mesChart',
            'veiculoChart',
            'ano',
            'mes',
            'anos',
            'meses'
        ));
    }

    public function pdf(Request $request)
    {
        $ano = (int) $request->get('ano', now()->year);
        $mes = $request->filled('mes') ? (int) $request->get('mes') : null;

        $query = SolicitacaoAbastecimento::with(['usuario', 'veiculo', 'aprovador'])
            ->whereYear('data_solicitacao', $ano)
            ->orderByDesc('data_solicitacao')
            ->orderByDesc('id');

        if ($mes && $mes >= 1 && $mes <= 12) {
            $query->whereMonth('data_solicitacao', $mes);
        }

        $solicitacoes = $query->get();

        $cards = [
            'total' => $solicitacoes->count(),
            'pendentes' => $solicitacoes->where('status', 'pendente')->count(),
            'aprovadas' => $solicitacoes->where('status', 'aprovada')->count(),
            'reprovadas' => $solicitacoes->where('status', 'reprovada')->count(),
            'ajustadas' => $solicitacoes->where('status', 'ajustada')->count(),

            'valor_aprovado' => $solicitacoes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'valor')
                ->sum(fn ($item) => (float) $item->quantidade_aprovada),

            'litros_aprovados' => $solicitacoes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'litros')
                ->sum(fn ($item) => (float) $item->quantidade_aprovada),
        ];

        $veiculosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->veiculo)
            ->groupBy(fn ($item) => $item->veiculo->id)
            ->count();

        $usuariosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->usuario)
            ->groupBy(fn ($item) => $item->usuario->id)
            ->count();

        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        $periodo = $mes ? ($meses[$mes] . '/' . $ano) : ('Ano de ' . $ano);

        $pdf = Pdf::loadView('abastecimento.painel.pdf', compact(
            'solicitacoes',
            'cards',
            'ano',
            'mes',
            'meses',
            'periodo',
            'veiculosUnicos',
            'usuariosUnicos'
        ))->setPaper('a4', 'landscape');

        $nomeArquivo = $mes
            ? 'relatorio-combustivel-' . $ano . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '.pdf'
            : 'relatorio-combustivel-' . $ano . '.pdf';

        return $pdf->stream($nomeArquivo);
    }
}