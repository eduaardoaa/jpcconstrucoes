<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Obra;
use App\Models\Produto;
use App\Models\ProdutoVariacao;

class EstoqueSyncService
{
    public function syncAll(): void
    {
        $obras = Obra::all();
        $produtos = Produto::with(['variacoes' => function ($query) {
            $query->orderBy('id');
        }])->get();

        foreach ($produtos as $produto) {
            $this->syncProdutoCentral($produto);
            $this->syncProdutoComObras($produto, $obras);
        }
    }

    public function syncForObra(Obra $obra): void
    {
        $produtos = Produto::with(['variacoes' => function ($query) {
            $query->orderBy('id');
        }])->get();

        foreach ($produtos as $produto) {
            $this->syncProdutoNaObra($produto, $obra);
        }
    }

    public function syncForProduto(Produto $produto): void
    {
        $produto->loadMissing(['variacoes' => function ($query) {
            $query->orderBy('id');
        }]);

        $obras = Obra::all();

        $this->syncProdutoCentral($produto);

        foreach ($obras as $obra) {
            $this->syncProdutoNaObra($produto, $obra);
        }
    }

    public function syncForVariacao(ProdutoVariacao $variacao): void
    {
        $variacao->loadMissing('produto');

        if (!$variacao->produto) {
            return;
        }

        $obras = Obra::all();

        $this->ensureEstoque(null, $variacao->produto_id, $variacao->id);

        foreach ($obras as $obra) {
            $this->ensureEstoque($obra->id, $variacao->produto_id, $variacao->id);
        }
    }

    private function syncProdutoComObras(Produto $produto, $obras): void
    {
        foreach ($obras as $obra) {
            $this->syncProdutoNaObra($produto, $obra);
        }
    }

    private function syncProdutoCentral(Produto $produto): void
    {
        $produto->loadMissing(['variacoes' => function ($query) {
            $query->orderBy('id');
        }]);

        if ($produto->controla_variacao && $produto->variacoes->count()) {
            foreach ($produto->variacoes as $variacao) {
                $this->ensureEstoque(null, $produto->id, $variacao->id);
            }
        } else {
            $this->ensureEstoque(null, $produto->id, null);
        }
    }

    private function syncProdutoNaObra(Produto $produto, Obra $obra): void
    {
        $produto->loadMissing(['variacoes' => function ($query) {
            $query->orderBy('id');
        }]);

        if ($produto->controla_variacao && $produto->variacoes->count()) {
            foreach ($produto->variacoes as $variacao) {
                $this->ensureEstoque($obra->id, $produto->id, $variacao->id);
            }
        } else {
            $this->ensureEstoque($obra->id, $produto->id, null);
        }
    }

    private function ensureEstoque(?int $obraId, int $produtoId, ?int $produtoVariacaoId): void
    {
        Estoque::firstOrCreate(
            [
                'obra_id' => $obraId,
                'produto_id' => $produtoId,
                'produto_variacao_id' => $produtoVariacaoId,
            ],
            [
                'quantidade_atual' => 0,
            ]
        );
    }
}