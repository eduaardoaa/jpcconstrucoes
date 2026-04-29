<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = [
    'nome',
    'descricao',
    'unidade',
    'valor_unitario',
    'dias_entrega_media',
    'controla_variacao',
    'ca',
    'status',
];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'controla_variacao' => 'boolean',
    ];

    public function variacoes(): HasMany
    {
        return $this->hasMany(ProdutoVariacao::class);
    }
    public function estoques()
{
    return $this->hasMany(Estoque::class);
}

public function movimentacoesEstoque()
{
    return $this->hasMany(MovimentacaoEstoque::class);
}
}