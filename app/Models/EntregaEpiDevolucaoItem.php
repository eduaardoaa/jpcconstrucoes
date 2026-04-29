<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaEpiDevolucaoItem extends Model
{
    protected $table = 'entrega_epi_devolucao_itens';

    protected $fillable = [
        'entrega_epi_devolucao_id',
        'produto_id',
        'produto_variacao_id',
        'quantidade',
    ];

    public function devolucao(): BelongsTo
    {
        return $this->belongsTo(EntregaEpiDevolucao::class, 'entrega_epi_devolucao_id');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function variacao(): BelongsTo
    {
        return $this->belongsTo(ProdutoVariacao::class, 'produto_variacao_id');
    }
}