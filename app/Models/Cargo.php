<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'cargo_permissao')
            ->withTimestamps();
    }

    public function hasPermissao(string $chave): bool
    {
        if ($this->tipo === 'funcionario') {
            return false;
        }

        return $this->permissoes()->where('chave', $chave)->exists();
    }
}