<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Veiculo extends Model
{
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'ano',
        'cor',
        'tipo_combustivel',
        'km_atual',
        'status',
        'observacao',
    ];

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(SolicitacaoAbastecimento::class);
    }

    public function getNomeCompletoAttribute(): string
    {
        return "{$this->placa} - {$this->marca} {$this->modelo}";
    }
}