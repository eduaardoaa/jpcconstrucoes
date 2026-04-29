<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Obra extends Model
{
    protected $fillable = [
        'nome',
        'endereco',
        'responsavel',
        'data_inicio',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
    ];

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class);
    }

    public function estoques(): HasMany
    {
        return $this->hasMany(Estoque::class);
    }

    public function movimentacoesEstoque(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }

    public function entregasEpi(): HasMany
    {
        return $this->hasMany(\App\Models\EntregaEpi::class);
    }

    public function treinamentosDds(): HasMany
    {
        return $this->hasMany(ObraTreinamentoDds::class)->orderByDesc('data_treinamento')->orderByDesc('id');
    }

    public function ultimoTreinamentoDds(): HasOne
    {
        return $this->hasOne(ObraTreinamentoDds::class)->latestOfMany('data_treinamento');
    }
}