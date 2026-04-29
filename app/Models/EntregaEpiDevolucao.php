<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntregaEpiDevolucao extends Model
{
    protected $table = 'entrega_epi_devolucoes';

    protected $fillable = [
        'entrega_epi_id',
        'entrega_origem_id',
        'funcionario_id',
        'obra_id',
        'user_id',
        'data_devolucao',
        'motivo',
    ];

    protected $casts = [
        'data_devolucao' => 'date',
    ];

    public function entrega(): BelongsTo
    {
        return $this->belongsTo(EntregaEpi::class, 'entrega_epi_id');
    }

    public function entregaOrigem(): BelongsTo
    {
        return $this->belongsTo(EntregaEpi::class, 'entrega_origem_id');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(EntregaEpiDevolucaoItem::class, 'entrega_epi_devolucao_id');
    }
}