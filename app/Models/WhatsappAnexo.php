<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappAnexo extends Model
{
    protected $table = 'whatsapp_anexos';

    protected $fillable = [
        'whatsapp_mensagem_id',
        'tipo',
        'nome_arquivo',
        'mime_type',
        'tamanho',
        'url_original',
        'caminho_local',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function mensagem(): BelongsTo
    {
        return $this->belongsTo(WhatsappMensagem::class, 'whatsapp_mensagem_id');
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->caminho_local) {
            return asset('storage/' . $this->caminho_local);
        }

        return $this->url_original;
    }
}