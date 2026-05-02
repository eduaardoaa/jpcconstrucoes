<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vaga extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'local',
        'tipo_contrato',
        'salario',
        'requisitos',
        'diferenciais',
        'beneficios',

        'slug',
        'token',
        'status',
        'data_limite',
        'user_id',
    ];

    protected $casts = [
        'data_limite' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Vaga $vaga) {
            if (empty($vaga->slug)) {
                $base = Str::slug($vaga->titulo);
                $slug = $base;
                $count = 1;

                while (Vaga::where('slug', $slug)->exists()) {
                    $count++;
                    $slug = $base . '-' . $count;
                }

                $vaga->slug = $slug;
            }
            if (empty($vaga->token)) {
                $vaga->token = Str::random(48);
            }
        });
    }

    // ─── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function perguntas(): HasMany
    {
        return $this->hasMany(VagaPergunta::class)->orderBy('ordem');
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(VagaCandidatura::class)->orderByDesc('created_at');
    }

    // ─── Helpers ────────────────────────────────────────────

    public function linkPublico(): string
    {
        return route('vagas.aplicar', $this->slug);
    }

    public function isAberta(): bool
    {
        if ($this->status !== 'aberta') return false;
        if ($this->data_limite && $this->data_limite->isPast()) return false;
        return true;
    }
}
