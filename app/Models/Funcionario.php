<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Funcionario extends Model
{
    protected $fillable = [
    'obra_id',
    'cargo_id',
    'nome',
    'cpf',
    'matricula', // 👈 ADD
    'telefone',
    'data_admissao',
    'status',
    'observacoes',
];

    protected $casts = [
        'data_admissao' => 'date',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }
    public function entregasEpi(): HasMany
{
    return $this->hasMany(\App\Models\EntregaEpi::class);
}
}