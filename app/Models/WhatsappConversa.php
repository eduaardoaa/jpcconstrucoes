<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappConversa extends Model
{
    protected $table = 'whatsapp_conversas';

    protected $fillable = [
        'whatsapp_instancia_id',
        'whatsapp_contato_id',
        'atendente_id',
        'status',
        'fixado',
        'nao_lidas',
        'ultima_mensagem_em',
        'ultima_mensagem_preview',
        'enviar_identificacao',
    ];

    protected $casts = [
        'ultima_mensagem_em'  => 'datetime',
        'enviar_identificacao' => 'boolean',
        'fixado'              => 'boolean',
    ];

    public function instancia(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstancia::class, 'whatsapp_instancia_id');
    }

    public function contato(): BelongsTo
    {
        return $this->belongsTo(WhatsappContato::class, 'whatsapp_contato_id');
    }

    public function atendente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(WhatsappMensagem::class, 'whatsapp_conversa_id');
    }

    public function ultimasMensagens(): HasMany
    {
        return $this->mensagens()->latest();
    }

    public function estaAberta(): bool
    {
        return $this->status === 'aberta';
    }
    
}