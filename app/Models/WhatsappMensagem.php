<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappMensagem extends Model
{
    protected $table = 'whatsapp_mensagens';

    protected $fillable = [
        'whatsapp_instancia_id',
        'whatsapp_conversa_id',
        'whatsapp_contato_id',
        'user_id',
        'message_id',
        'participant',
        'reply_to_message_id',
        'direcao',
        'tipo',
        'conteudo',
        'payload',
        'status_envio',
        'enviada_em',
        'apagada_em',
    ];

    protected $casts = [
        'payload'    => 'array',
        'enviada_em' => 'datetime',
        'apagada_em' => 'datetime',
    ];

    public function instancia(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstancia::class, 'whatsapp_instancia_id');
    }

    public function conversa(): BelongsTo
    {
        return $this->belongsTo(WhatsappConversa::class, 'whatsapp_conversa_id');
    }

    public function contato(): BelongsTo
    {
        return $this->belongsTo(WhatsappContato::class, 'whatsapp_contato_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(WhatsappAnexo::class, 'whatsapp_mensagem_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(WhatsappMensagem::class, 'reply_to_message_id');
    }

    public function enviadaPorAtendente(): bool
    {
        return $this->direcao === 'saida';
    }

    public function recebidaDoCliente(): bool
    {
        return $this->direcao === 'entrada';
    }

    /**
     * Extrai o número do JID do participant (ex: 5511999@s.whatsapp.net → 5511999)
     */
    public function getParticipantNumeroAttribute(): ?string
    {
        if (!$this->participant) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', str_replace('@s.whatsapp.net', '', $this->participant));
    }
}
