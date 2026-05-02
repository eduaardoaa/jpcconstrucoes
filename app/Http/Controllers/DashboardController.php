<?php

namespace App\Http\Controllers;

use App\Models\EntregaEpi;
use App\Models\EntregaEpiItem;
use App\Models\Estoque;
use App\Models\Funcionario;
use App\Models\Obra;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $podeVerEstoque = $user->hasPermissao('estoque');
        $podeVerEntregas = $user->hasPermissao('entregas_epi');
        $podeVerObras = $user->hasPermissao('obras');
        $podeVerFuncionarios = $user->hasPermissao('funcionarios');
        $podeVerRelatorios = $user->hasPermissao('relatorios');
        $podeVerProdutos = $user->hasPermissao('produtos');

        $dataInicio = $request->get('data_inicio')
            ? Carbon::parse($request->get('data_inicio'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $dataFim = $request->get('data_fim')
            ? Carbon::parse($request->get('data_fim'))->endOfDay()
            : now()->endOfMonth()->endOfDay();

        $diasPeriodo = (int) $dataInicio->copy()->startOfDay()->diffInDays($dataFim->copy()->startOfDay()) + 1;
        $inicio7Dias = now()->subDays(6)->startOfDay()->toDateString();
        $fimHoje = now()->endOfDay()->toDateString();

        $totalFuncionarios = $podeVerFuncionarios
            ? Funcionario::where('status', 'ativo')->count()
            : null;

        $totalObras = $podeVerObras
            ? Obra::where('status', 'ativa')->count()
            : null;

        $totalProdutos = $podeVerProdutos
            ? Produto::where('status', 'ativo')->count()
            : null;

        $totalEntregasPeriodo = $podeVerEntregas
            ? EntregaEpi::whereBetween('data_entrega', [
                $dataInicio->toDateString(),
                $dataFim->toDateString(),
            ])->count()
            : null;

        $totalItensEntreguesPeriodo = $podeVerEntregas
            ? (int) EntregaEpiItem::whereHas('entrega', function ($query) use ($dataInicio, $dataFim) {
                $query->whereBetween('data_entrega', [
                    $dataInicio->toDateString(),
                    $dataFim->toDateString(),
                ]);
            })->sum('quantidade')
            : null;

        $totalEntregasMes = $podeVerEntregas
            ? EntregaEpi::whereBetween('data_entrega', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->count()
            : null;

        $totalItensMes = $podeVerEntregas
            ? (int) EntregaEpiItem::whereHas('entrega', function ($query) {
                $query->whereBetween('data_entrega', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ]);
            })->sum('quantidade')
            : null;

        $entregasPendentesComprovante = $podeVerEntregas
            ? EntregaEpi::where('status_comprovante', 'pendente')->count()
            : null;

        $estoqueTotal = $podeVerEstoque
            ? (int) Estoque::sum('quantidade_atual')
            : null;

        $zeroStockItems = collect();
        $criticalStockItems = collect();
        $consumoPorObra = collect();
        $produtosMaisEntregues = collect();
        $funcionariosMaisRetiradas = collect();
        $entregasPendentesLista = collect();
        $stockCoverage = collect();
        $ordersByDay = collect();
        $itemsOutByDay = collect();
        $criticalAlerts = collect();
        $topObraConsumo = null;
        $topProdutoConsumo = null;

        if ($podeVerEstoque) {
            $zeroStockItems = Estoque::with(['produto', 'variacao', 'obra'])
                ->where('quantidade_atual', '<=', 0)
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->produto->nome ?? '-',
                        'variant_name' => $this->nomeVariacao($item->variacao),
                        'obra_name' => $item->obra->nome ?? '-',
                        'stock' => (int) $item->quantidade_atual,
                    ];
                });

            $criticalStockItems = Estoque::with(['produto', 'variacao', 'obra'])
                ->where('quantidade_atual', '>', 0)
                ->where('quantidade_atual', '<=', 10)
                ->orderBy('quantidade_atual')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->produto->nome ?? '-',
                        'variant_name' => $this->nomeVariacao($item->variacao),
                        'obra_name' => $item->obra->nome ?? '-',
                        'stock' => (int) $item->quantidade_atual,
                    ];
                });

            $stockCoverage = Estoque::select('obra_id', 'produto_id', 'produto_variacao_id')
                ->groupBy('obra_id', 'produto_id', 'produto_variacao_id')
                ->get()
                ->map(function ($estoque) use ($inicio7Dias, $fimHoje) {
                    $obra = Obra::find($estoque->obra_id);
                    $produto = Produto::find($estoque->produto_id);
                    $variacao = $estoque->produto_variacao_id ? ProdutoVariacao::find($estoque->produto_variacao_id) : null;

                    $estoqueAtual = (int) Estoque::where('obra_id', $estoque->obra_id)
                        ->where('produto_id', $estoque->produto_id)
                        ->where(function ($query) use ($estoque) {
                            if ($estoque->produto_variacao_id) {
                                $query->where('produto_variacao_id', $estoque->produto_variacao_id);
                            } else {
                                $query->whereNull('produto_variacao_id');
                            }
                        })
                        ->sum('quantidade_atual');

                    $consumo7Dias = (int) EntregaEpiItem::join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
                        ->where('entregas_epi.obra_id', $estoque->obra_id)
                        ->where('entrega_epi_itens.produto_id', $estoque->produto_id)
                        ->where(function ($query) use ($estoque) {
                            if ($estoque->produto_variacao_id) {
                                $query->where('entrega_epi_itens.produto_variacao_id', $estoque->produto_variacao_id);
                            } else {
                                $query->whereNull('entrega_epi_itens.produto_variacao_id');
                            }
                        })
                        ->whereBetween('entregas_epi.data_entrega', [$inicio7Dias, $fimHoje])
                        ->sum('entrega_epi_itens.quantidade');

                    $mediaDiaria = round($consumo7Dias / 7, 2);
                    $coverage = $mediaDiaria > 0 ? round($estoqueAtual / $mediaDiaria, 1) : null;

                    return [
                        'obra_name' => $obra->nome ?? '-',
                        'product_name' => $produto->nome ?? '-',
                        'variant_name' => $this->nomeVariacao($variacao),
                        'stock' => $estoqueAtual,
                        'avg_daily' => $mediaDiaria,
                        'coverage_days' => $coverage,
                    ];
                })
                ->sortBy(function ($item) {
                    return $item['coverage_days'] === null ? 999999 : $item['coverage_days'];
                })
                ->values();
        }

        if ($podeVerEntregas && $podeVerObras) {
            $consumoPorObra = EntregaEpiItem::selectRaw('entregas_epi.obra_id, SUM(entrega_epi_itens.quantidade) as total')
                ->join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
                ->whereBetween('entregas_epi.data_entrega', [
                    $dataInicio->toDateString(),
                    $dataFim->toDateString(),
                ])
                ->groupBy('entregas_epi.obra_id')
                ->orderByDesc('total')
                ->get()
                ->map(function ($item) {
                    $obra = Obra::find($item->obra_id);

                    return (object) [
                        'obra_nome' => $obra->nome ?? '-',
                        'total' => (int) $item->total,
                    ];
                });

            $topObraConsumo = $consumoPorObra->first();
        }

        if ($podeVerEntregas && $podeVerProdutos) {
            $produtosMaisEntregues = EntregaEpiItem::selectRaw('produto_id, produto_variacao_id, SUM(quantidade) as total')
                ->whereHas('entrega', function ($query) use ($dataInicio, $dataFim) {
                    $query->whereBetween('data_entrega', [
                        $dataInicio->toDateString(),
                        $dataFim->toDateString(),
                    ]);
                })
                ->groupBy('produto_id', 'produto_variacao_id')
                ->orderByDesc('total')
                ->limit(12)
                ->get()
                ->map(function ($item) {
                    $produto = Produto::find($item->produto_id);
                    $variacao = $item->produto_variacao_id ? ProdutoVariacao::find($item->produto_variacao_id) : null;

                    return [
                        'product_name' => $produto->nome ?? '-',
                        'variant_name' => $this->nomeVariacao($variacao),
                        'total' => (int) $item->total,
                    ];
                });

            $topProdutoConsumo = $produtosMaisEntregues->first();
        }

        if ($podeVerEntregas && $podeVerFuncionarios) {
            $funcionariosMaisRetiradas = EntregaEpiItem::selectRaw('entregas_epi.funcionario_id, SUM(entrega_epi_itens.quantidade) as total')
                ->join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
                ->whereBetween('entregas_epi.data_entrega', [
                    $dataInicio->toDateString(),
                    $dataFim->toDateString(),
                ])
                ->groupBy('entregas_epi.funcionario_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $funcionario = Funcionario::find($item->funcionario_id);

                    return (object) [
                        'funcionario_nome' => $funcionario->nome ?? '-',
                        'total' => (int) $item->total,
                    ];
                });
        }

        if ($podeVerEntregas) {
            $entregasPendentesLista = EntregaEpi::with(['funcionario', 'obra'])
                ->where('status_comprovante', 'pendente')
                ->orderByDesc('data_entrega')
                ->limit(10)
                ->get();

            for ($i = 6; $i >= 0; $i--) {
                $dia = now()->subDays($i)->toDateString();

                $pedidosDia = EntregaEpi::whereDate('data_entrega', $dia)->count();

                $itensDia = (int) EntregaEpiItem::whereHas('entrega', function ($query) use ($dia) {
                    $query->whereDate('data_entrega', $dia);
                })->sum('quantidade');

                $ordersByDay->push([
                    'label' => Carbon::parse($dia)->format('d/m'),
                    'value' => $pedidosDia,
                ]);

                $itemsOutByDay->push([
                    'label' => Carbon::parse($dia)->format('d/m'),
                    'value' => $itensDia,
                ]);
            }
        }

        if ($podeVerEntregas && $entregasPendentesComprovante > 0) {
            $criticalAlerts->push("Existem {$entregasPendentesComprovante} entrega(s) com comprovante pendente.");
        }

        if ($podeVerEstoque && $zeroStockItems->count() > 0) {
            $criticalAlerts->push("Existem {$zeroStockItems->count()} item(ns) zerado(s) no estoque.");
        }

        if ($podeVerEstoque && $criticalStockItems->count() > 0) {
            $criticalAlerts->push("Existem {$criticalStockItems->count()} item(ns) com estoque crítico (até 10 unidades).");
        }

        return view('dashboard', compact(
            'podeVerEstoque',
            'podeVerEntregas',
            'podeVerObras',
            'podeVerFuncionarios',
            'podeVerRelatorios',
            'podeVerProdutos',
            'dataInicio',
            'dataFim',
            'diasPeriodo',
            'totalFuncionarios',
            'totalObras',
            'totalProdutos',
            'totalEntregasPeriodo',
            'totalItensEntreguesPeriodo',
            'totalEntregasMes',
            'totalItensMes',
            'entregasPendentesComprovante',
            'estoqueTotal',
            'zeroStockItems',
            'criticalStockItems',
            'consumoPorObra',
            'produtosMaisEntregues',
            'funcionariosMaisRetiradas',
            'entregasPendentesLista',
            'topObraConsumo',
            'topProdutoConsumo',
            'stockCoverage',
            'ordersByDay',
            'itemsOutByDay',
            'criticalAlerts'
        ));
    }

    private function nomeVariacao($variacao): ?string
    {
        if (!$variacao) {
            return null;
        }

        $partes = array_filter([
            $variacao->nome_variacao ?? null,
            $variacao->cor ?? null,
            $variacao->tamanho ?? null,
        ]);

        $texto = trim(implode(' ', $partes));

        if (!empty($variacao->sku)) {
            $texto .= ' | SKU: ' . $variacao->sku;
        }

        return $texto ?: null;
    }
}