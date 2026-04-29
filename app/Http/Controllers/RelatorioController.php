<?php

namespace App\Http\Controllers;

use App\Models\EntregaEpi;
use App\Models\EntregaEpiItem;
use App\Models\Estoque;
use App\Models\Funcionario;
use App\Models\Obra;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index()
    {
        $totalEntregas = EntregaEpi::count();
        $totalFuncionariosAtivos = Funcionario::where('status', 'ativo')->count();
        $totalComprovantesPendentes = EntregaEpi::where('status_comprovante', 'pendente')->count();

        $itensEntreguesMes = (int) EntregaEpiItem::join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
            ->whereBetween('entregas_epi.data_entrega', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->sum('quantidade');

        return view('relatorios.index', compact(
            'totalEntregas',
            'totalFuncionariosAtivos',
            'totalComprovantesPendentes',
            'itensEntreguesMes'
        ));
    }

    public function estoquePorObra(Request $request)
    {
        $dataInicio = $request->get('data_inicio')
            ? Carbon::parse($request->get('data_inicio'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $dataFim = $request->get('data_fim')
            ? Carbon::parse($request->get('data_fim'))->endOfDay()
            : now()->endOfMonth()->endOfDay();

        $obraId = $request->get('obra_id');
        $produtoId = $request->get('produto_id');

        $diasPeriodo = max(1, $dataInicio->diffInDays($dataFim) + 1);

        $obras = Obra::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();

        $estoques = Estoque::with(['obra', 'produto', 'variacao'])
            ->when($obraId, function ($query) use ($obraId) {
                $query->where('obra_id', $obraId);
            })
            ->when($produtoId, function ($query) use ($produtoId) {
                $query->where('produto_id', $produtoId);
            })
            ->get()
            ->map(function ($estoque) use ($dataInicio, $dataFim, $diasPeriodo) {
                $totalEntregue = (int) EntregaEpiItem::join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
                    ->where('entregas_epi.obra_id', $estoque->obra_id)
                    ->where('entrega_epi_itens.produto_id', $estoque->produto_id)
                    ->where(function ($query) use ($estoque) {
                        if ($estoque->produto_variacao_id) {
                            $query->where('entrega_epi_itens.produto_variacao_id', $estoque->produto_variacao_id);
                        } else {
                            $query->whereNull('entrega_epi_itens.produto_variacao_id');
                        }
                    })
                    ->whereBetween('entregas_epi.data_entrega', [
                        $dataInicio->toDateString(),
                        $dataFim->toDateString(),
                    ])
                    ->sum('entrega_epi_itens.quantidade');

                $mediaDiaria = $diasPeriodo > 0 ? round($totalEntregue / $diasPeriodo, 2) : 0;
                $diasRestantes = $mediaDiaria > 0
                    ? round(((float) $estoque->quantidade_atual / $mediaDiaria), 1)
                    : null;

                return [
                    'obra_nome' => $estoque->obra->nome ?? '-',
                    'produto_nome' => $estoque->produto->nome ?? '-',
                    'variacao_nome' => $this->nomeVariacao($estoque->variacao),
                    'estoque_atual' => (int) $estoque->quantidade_atual,
                    'total_entregue' => $totalEntregue,
                    'media_diaria' => $mediaDiaria,
                    'dias_restantes' => $diasRestantes,
                ];
            })
            ->sortBy([
                ['obra_nome', 'asc'],
                ['produto_nome', 'asc'],
            ])
            ->values();

        return view('relatorios.estoque-obra', compact(
            'obras',
            'produtos',
            'estoques',
            'obraId',
            'produtoId',
            'dataInicio',
            'dataFim',
            'diasPeriodo'
        ));
    }

    public function estoquePorObraPdf(Request $request)
    {
        $dataInicio = $request->get('data_inicio')
            ? Carbon::parse($request->get('data_inicio'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $dataFim = $request->get('data_fim')
            ? Carbon::parse($request->get('data_fim'))->endOfDay()
            : now()->endOfMonth()->endOfDay();

        $obraId = $request->get('obra_id');
        $produtoId = $request->get('produto_id');

        $diasPeriodo = max(1, $dataInicio->diffInDays($dataFim) + 1);

        $estoques = Estoque::with(['obra', 'produto', 'variacao'])
            ->when($obraId, function ($query) use ($obraId) {
                $query->where('obra_id', $obraId);
            })
            ->when($produtoId, function ($query) use ($produtoId) {
                $query->where('produto_id', $produtoId);
            })
            ->get()
            ->map(function ($estoque) use ($dataInicio, $dataFim, $diasPeriodo) {
                $totalEntregue = (int) EntregaEpiItem::join('entregas_epi', 'entregas_epi.id', '=', 'entrega_epi_itens.entrega_epi_id')
                    ->where('entregas_epi.obra_id', $estoque->obra_id)
                    ->where('entrega_epi_itens.produto_id', $estoque->produto_id)
                    ->where(function ($query) use ($estoque) {
                        if ($estoque->produto_variacao_id) {
                            $query->where('entrega_epi_itens.produto_variacao_id', $estoque->produto_variacao_id);
                        } else {
                            $query->whereNull('entrega_epi_itens.produto_variacao_id');
                        }
                    })
                    ->whereBetween('entregas_epi.data_entrega', [
                        $dataInicio->toDateString(),
                        $dataFim->toDateString(),
                    ])
                    ->sum('entrega_epi_itens.quantidade');

                $mediaDiaria = $diasPeriodo > 0 ? round($totalEntregue / $diasPeriodo, 2) : 0;
                $diasRestantes = $mediaDiaria > 0
                    ? round(((float) $estoque->quantidade_atual / $mediaDiaria), 1)
                    : null;

                return [
                    'obra_nome' => $estoque->obra->nome ?? '-',
                    'produto_nome' => $estoque->produto->nome ?? '-',
                    'variacao_nome' => $this->nomeVariacao($estoque->variacao),
                    'estoque_atual' => (int) $estoque->quantidade_atual,
                    'total_entregue' => $totalEntregue,
                    'media_diaria' => $mediaDiaria,
                    'dias_restantes' => $diasRestantes,
                ];
            })
            ->sortBy([
                ['obra_nome', 'asc'],
                ['produto_nome', 'asc'],
            ])
            ->values();

        $pdf = Pdf::loadView('relatorios.pdf.estoque-obra', [
            'estoques' => $estoques,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-estoque-por-obra.pdf');
    }

    public function funcionarios()
    {
        $dados = Funcionario::with([
            'obra',
            'cargo',
            'entregasEpi.itens',
        ])
            ->orderBy('nome')
            ->get()
            ->map(function ($funcionario) {
                $entregasOrdenadas = $funcionario->entregasEpi->sortByDesc('data_entrega');
                $ultimaEntrega = $entregasOrdenadas->first();

                return [
                    'nome' => $funcionario->nome,
                    'cpf' => $funcionario->cpf,
                    'matricula' => $funcionario->matricula,
                    'telefone' => $funcionario->telefone,
                    'obra' => $funcionario->obra->nome ?? '-',
                    'cargo' => $funcionario->cargo->nome ?? '-',
                    'status' => $funcionario->status,
                    'data_admissao' => $funcionario->data_admissao,
                    'total_entregas' => $funcionario->entregasEpi->count(),
                    'total_itens' => $funcionario->entregasEpi->flatMap->itens->sum('quantidade'),
                    'ultima_entrega' => $ultimaEntrega?->data_entrega,
                ];
            });

        return view('relatorios.funcionarios', compact('dados'));
    }

    public function funcionariosPdf()
    {
        $dados = Funcionario::with([
            'obra',
            'cargo',
            'entregasEpi.itens',
        ])
            ->orderBy('nome')
            ->get()
            ->map(function ($funcionario) {
                $entregasOrdenadas = $funcionario->entregasEpi->sortByDesc('data_entrega');
                $ultimaEntrega = $entregasOrdenadas->first();

                return [
                    'nome' => $funcionario->nome,
                    'cpf' => $funcionario->cpf,
                    'matricula' => $funcionario->matricula,
                    'telefone' => $funcionario->telefone,
                    'obra' => $funcionario->obra->nome ?? '-',
                    'cargo' => $funcionario->cargo->nome ?? '-',
                    'status' => $funcionario->status,
                    'data_admissao' => $funcionario->data_admissao,
                    'total_entregas' => $funcionario->entregasEpi->count(),
                    'total_itens' => $funcionario->entregasEpi->flatMap->itens->sum('quantidade'),
                    'ultima_entrega' => $ultimaEntrega?->data_entrega,
                ];
            });

        $pdf = Pdf::loadView('relatorios.pdf.funcionarios', compact('dados'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-entregas-por-funcionario.pdf');
    }

    public function consumo()
    {
        $dados = EntregaEpiItem::selectRaw('produto_id, produto_variacao_id, SUM(quantidade) as total')
            ->with(['produto', 'variacao'])
            ->groupBy('produto_id', 'produto_variacao_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'produto' => $item->produto->nome ?? '-',
                    'variacao' => $this->nomeVariacao($item->variacao),
                    'total' => (int) $item->total,
                ];
            });

        return view('relatorios.consumo', compact('dados'));
    }

    public function consumoPdf()
    {
        $dados = EntregaEpiItem::selectRaw('produto_id, produto_variacao_id, SUM(quantidade) as total')
            ->with(['produto', 'variacao'])
            ->groupBy('produto_id', 'produto_variacao_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'produto' => $item->produto->nome ?? '-',
                    'variacao' => $this->nomeVariacao($item->variacao),
                    'total' => (int) $item->total,
                ];
            });

        $pdf = Pdf::loadView('relatorios.pdf.consumo', compact('dados'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('relatorio-consumo-por-produto.pdf');
    }

    public function comprovantes()
    {
        $dados = EntregaEpi::with(['funcionario', 'obra', 'usuario'])
            ->where('status_comprovante', 'pendente')
            ->orderByDesc('data_entrega')
            ->get();

        return view('relatorios.comprovantes', compact('dados'));
    }

    public function comprovantesPdf()
    {
        $dados = EntregaEpi::with(['funcionario', 'obra', 'usuario'])
            ->where('status_comprovante', 'pendente')
            ->orderByDesc('data_entrega')
            ->get();

        $pdf = Pdf::loadView('relatorios.pdf.comprovantes', compact('dados'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('relatorio-comprovantes-pendentes.pdf');
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