<?php

namespace App\Http\Controllers;

use App\Models\MovimentacaoEstoque;
use App\Models\Obra;
use App\Models\Produto;
use Illuminate\Http\Request;

class MovimentacaoEstoqueController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->get('obra_id');
        $produtoId = $request->get('produto_id');
        $tipo = $request->get('tipo_movimentacao');
        $dataInicial = $request->get('data_inicial');
        $dataFinal = $request->get('data_final');

        $obras = Obra::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();

        $movimentacoes = MovimentacaoEstoque::with([
            'obra',
            'produto',
            'variacao',
            'usuario',
        ])
            ->when($obraId, function ($query) use ($obraId) {
                $query->where('obra_id', $obraId);
            })
            ->when($produtoId, function ($query) use ($produtoId) {
                $query->where('produto_id', $produtoId);
            })
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('tipo_movimentacao', $tipo);
            })
            ->when($dataInicial, function ($query) use ($dataInicial) {
                $query->whereDate('data_movimentacao', '>=', $dataInicial);
            })
            ->when($dataFinal, function ($query) use ($dataFinal) {
                $query->whereDate('data_movimentacao', '<=', $dataFinal);
            })
            ->orderByDesc('data_movimentacao')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('estoque.historico', compact(
            'movimentacoes',
            'obras',
            'produtos',
            'obraId',
            'produtoId',
            'tipo',
            'dataInicial',
            'dataFinal'
        ));
    }
}