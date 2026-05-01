<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAbastecimento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbastecimentoPainelController extends Controller
{
    public function index(Request $request)
    {
        $mes = (int) $request->get('mes', now()->month);
        $ano = (int) $request->get('ano', now()->year);

        if ($mes < 1 || $mes > 12) {
            $mes = now()->month;
        }

        if ($ano < 2000 || $ano > 2100) {
            $ano = now()->year;
        }

        $inicioMes = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fimMes = Carbon::create($ano, $mes, 1)->endOfMonth();

        $inicioMesAnterior = (clone $inicioMes)->subMonth()->startOfMonth();
        $fimMesAnterior = (clone $inicioMes)->subMonth()->endOfMonth();

        $baseQuery = SolicitacaoAbastecimento::with(['usuario', 'veiculo', 'aprovador']);

        $solicitacoesMes = (clone $baseQuery)
            ->whereBetween('data_solicitacao', [$inicioMes->toDateString(), $fimMes->toDateString()])
            ->orderByDesc('data_solicitacao')
            ->orderByDesc('id')
            ->get();

        $solicitacoesMesAnterior = (clone $baseQuery)
            ->whereBetween('data_solicitacao', [$inicioMesAnterior->toDateString(), $fimMesAnterior->toDateString()])
            ->get();

        $cards = [
            'total' => $solicitacoesMes->count(),
            'pendentes' => $solicitacoesMes->where('status', 'pendente')->count(),
            'aprovadas' => $solicitacoesMes->where('status', 'aprovada')->count(),
            'reprovadas' => $solicitacoesMes->where('status', 'reprovada')->count(),
            'ajustadas' => $solicitacoesMes->where('status', 'ajustada')->count(),
            'valor_aprovado' => $solicitacoesMes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'valor')
                ->sum('quantidade_aprovada'),
            'litros_aprovados' => $solicitacoesMes
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'litros')
                ->sum('quantidade_aprovada'),
        ];

        $comparativoAnterior = [
            'total' => $solicitacoesMesAnterior->count(),
            'valor_aprovado' => $solicitacoesMesAnterior
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'valor')
                ->sum('quantidade_aprovada'),
            'litros_aprovados' => $solicitacoesMesAnterior
                ->whereIn('status', ['aprovada', 'ajustada'])
                ->where('tipo_solicitacao', 'litros')
                ->sum('quantidade_aprovada'),
        ];

        $variacoes = [
            'total' => $this->calcularVariacao($cards['total'], $comparativoAnterior['total']),
            'valor_aprovado' => $this->calcularVariacao($cards['valor_aprovado'], $comparativoAnterior['valor_aprovado']),
            'litros_aprovados' => $this->calcularVariacao($cards['litros_aprovados'], $comparativoAnterior['litros_aprovados']),
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

        $diasNoMes = $inicioMes->daysInMonth;
        $labelsDias = [];
        $valoresDias = [];

        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $labelsDias[] = str_pad($dia, 2, '0', STR_PAD_LEFT);

            $valoresDias[] = $solicitacoesMes
                ->filter(function ($item) use ($dia) {
                    return optional($item->data_solicitacao)->day === $dia;
                })
                ->count();
        }

        $diaChart = [
            'labels' => $labelsDias,
            'values' => $valoresDias,
        ];

        $comparativoMensalChart = [
            'labels' => ['Solicitações', 'Valor aprovado', 'Litros aprovados'],
            'atual' => [
                $cards['total'],
                round((float) $cards['valor_aprovado'], 2),
                round((float) $cards['litros_aprovados'], 2),
            ],
            'anterior' => [
                $comparativoAnterior['total'],
                round((float) $comparativoAnterior['valor_aprovado'], 2),
                round((float) $comparativoAnterior['litros_aprovados'], 2),
            ],
        ];

        $veiculosAgrupados = $solicitacoesMes
            ->filter(fn ($item) => $item->veiculo)
            ->groupBy(fn ($item) => $item->veiculo->placa . ' - ' . $item->veiculo->marca . ' ' . $item->veiculo->modelo)
            ->map(fn ($grupo) => $grupo->count())
            ->sortDesc()
            ->take(7);

        $veiculoChart = [
            'labels' => $veiculosAgrupados->keys()->values()->toArray(),
            'values' => $veiculosAgrupados->values()->toArray(),
        ];

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

        $anos = range(now()->year - 5, now()->year + 1);

        $mesAnteriorLabel = mb_strtolower($meses[(int) $inicioMesAnterior->month]) . '/' . $inicioMesAnterior->year;
        $mesAtualLabel = mb_strtolower($meses[$mes]) . '/' . $ano;

        return view('abastecimento.painel.index', compact(
            'mes',
            'ano',
            'meses',
            'anos',
            'cards',
            'variacoes',
            'solicitacoesMes',
            'statusChart',
            'diaChart',
            'comparativoMensalChart',
            'veiculoChart',
            'mesAnteriorLabel',
            'mesAtualLabel'
        ));
    }

    protected function calcularVariacao($atual, $anterior): array
    {
        $atual = (float) $atual;
        $anterior = (float) $anterior;

        if ($anterior == 0.0 && $atual == 0.0) {
            return [
                'percentual' => 0,
                'direcao' => 'neutro',
                'texto' => 'Sem variação',
            ];
        }

        if ($anterior == 0.0) {
            return [
                'percentual' => 100,
                'direcao' => 'subiu',
                'texto' => 'Alta de 100%',
            ];
        }

        $percentual = (($atual - $anterior) / $anterior) * 100;

        return [
            'percentual' => round(abs($percentual), 1),
            'direcao' => $percentual > 0 ? 'subiu' : ($percentual < 0 ? 'caiu' : 'neutro'),
            'texto' => $percentual > 0
                ? 'Alta de ' . round(abs($percentual), 1) . '%'
                : ($percentual < 0
                    ? 'Queda de ' . round(abs($percentual), 1) . '%'
                    : 'Sem variação'),
        ];
    }
}