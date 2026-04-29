<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdutoVariacao extends Model
{
    protected $table = 'produto_variacoes';

    protected $fillable = [
    'produto_id',
    'nome_variacao',
    'cor',
    'tamanho',
    'ca',
    'status',
];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
    public function estoques()
{
    return $this->hasMany(Estoque::class, 'produto_variacao_id');
}

public function movimentacoesEstoque()
{
    return $this->hasMany(MovimentacaoEstoque::class, 'produto_variacao_id');
}
}