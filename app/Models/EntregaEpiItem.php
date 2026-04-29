<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaEpiItem extends Model
{
    protected $table = 'entrega_epi_itens';

    protected $fillable = [
        'entrega_epi_id',
        'produto_id',
        'produto_variacao_id',
        'quantidade',
    ];

    public function entrega(): BelongsTo
    {
        return $this->belongsTo(EntregaEpi::class, 'entrega_epi_id');
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