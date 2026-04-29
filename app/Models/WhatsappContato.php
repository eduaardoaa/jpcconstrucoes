<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappContato extends Model
{
    protected $table = 'whatsapp_contatos';

    protected $fillable = [
        'whatsapp_instancia_id',
        'remote_jid',
        'lid_jid',
        'numero',
        'nome',
        'push_name',
        'foto_url',
        'is_grupo',
    ];

    protected $casts = [
        'is_grupo' => 'boolean',
    ];

    public function instancia(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstancia::class, 'whatsapp_instancia_id');
    }

    public function conversas(): HasMany
    {
        return $this->hasMany(WhatsappConversa::class, 'whatsapp_contato_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(WhatsappMensagem::class, 'whatsapp_contato_id');
    }

    public function getNomeExibicaoAttribute(): string
    {
        if ($this->is_grupo) {
            return $this->nome
                ?: $this->push_name
                ?: 'Grupo sem nome';
        }

        $isLid = str_contains($this->remote_jid ?? '', '@lid');

        return $this->nome
            ?: $this->push_name
            ?: ($isLid ? 'Número privado' : ($this->numero ?: $this->remote_jid));
    }

    public function getNumeroExibicaoAttribute(): string
    {
        if (str_contains($this->remote_jid ?? '', '@lid')) {
            return 'Número privado';
        }
        return $this->numero ?? preg_replace('/@.+$/', '', $this->remote_jid ?? '');
    }
}