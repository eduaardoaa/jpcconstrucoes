<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntregaEpi extends Model
{
    protected $table = 'entregas_epi';

    protected $fillable = [
        'funcionario_id',
        'obra_id',
        'user_id',
        'data_entrega',
        'status_comprovante',
        'observacoes',
    ];

    protected $casts = [
        'data_entrega' => 'date',
    ];

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
        return $this->hasMany(EntregaEpiItem::class, 'entrega_epi_id');
    }

    public function comprovantes(): HasMany
    {
        return $this->hasMany(EntregaEpiComprovante::class, 'entrega_epi_id');
    }
    public function devolucoes()
{
    return $this->hasMany(\App\Models\EntregaEpiDevolucao::class, 'entrega_epi_id');
}

public function devolucaoOrigem()
{
    return $this->hasMany(\App\Models\EntregaEpiDevolucao::class, 'entrega_origem_id');
}
}