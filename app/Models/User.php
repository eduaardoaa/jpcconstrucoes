<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo_id',
        'cpf',
        'telefone',
        'status',
        'primeiro_acesso',
        'pode_ter_veiculo',
        'veiculo_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'primeiro_acesso' => 'boolean',
            'pode_ter_veiculo' => 'boolean',
        ];
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function deslocamentosVeiculo(): HasMany
    {
        return $this->hasMany(DeslocamentoVeiculo::class, 'user_id');
    }

    public function whatsappInstancias(): BelongsToMany
    {
        return $this->belongsToMany(WhatsappInstancia::class, 'user_whatsapp_instancia')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        $nomeCargo = str($this->cargo?->nome ?? '')
            ->ascii()
            ->lower()
            ->trim()
            ->toString();

        return (int) $this->cargo_id === 1 || $nomeCargo === 'administrador';
    }

    public function isEngenheiro(): bool
    {
        return optional($this->cargo)->nome === 'Engenheiro';
    }

    public function hasPermissao(string $chave): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->cargo && $this->cargo->hasPermissao($chave);
    }

    public function podeAcessarWhatsapp(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->status === 'ativo'
            && $this->whatsappInstancias()
                ->where('whatsapp_instancias.status', 'ativa')
                ->exists();
    }

    public function whatsappInstanciasAtivas(): Builder|BelongsToMany
    {
        if ($this->isAdmin()) {
            return WhatsappInstancia::query()
                ->where('status', 'ativa')
                ->orderBy('nome');
        }

        return $this->whatsappInstancias()
            ->where('whatsapp_instancias.status', 'ativa')
            ->orderBy('whatsapp_instancias.nome');
    }

    public function podeSolicitarAbastecimento(): bool
    {
        return $this->status === 'ativo'
            && $this->pode_ter_veiculo
            && !is_null($this->veiculo_id)
            && optional($this->veiculo)->status === 'ativo';
    }
}