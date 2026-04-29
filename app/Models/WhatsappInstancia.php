<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappInstancia extends Model
{
    protected $table = 'whatsapp_instancias';

    protected $fillable = [
        'nome',
        'instance_name',
        'jid_proprio',
        'api_url',
        'api_key',
        'webhook_token',
        'status',
        'observacoes',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_whatsapp_instancia')
            ->withTimestamps();
    }

    public function contatos(): HasMany
    {
        return $this->hasMany(WhatsappContato::class, 'whatsapp_instancia_id');
    }

    public function conversas(): HasMany
    {
        return $this->hasMany(WhatsappConversa::class, 'whatsapp_instancia_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(WhatsappMensagem::class, 'whatsapp_instancia_id');
    }

    public function getWebhookUrlAttribute(): string
    {
        return url('/api/whatsapp/webhook/' . $this->webhook_token);
    }

    public function estaAtiva(): bool
    {
        return $this->status === 'ativa';
    }
}