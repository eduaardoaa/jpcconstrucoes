<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = [
        'nome',
        'chave',
        'descricao',
    ];

    public function cargos(): BelongsToMany
    {
        return $this->belongsToMany(Cargo::class, 'cargo_permissao')
            ->withTimestamps();
    }
}