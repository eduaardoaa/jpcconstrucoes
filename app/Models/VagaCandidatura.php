<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VagaCandidatura extends Model
{
    protected $fillable = [
        'vaga_id',
        'nome',
        'email',
        'telefone',
        'curriculo_path',
        'curriculo_nome_original',
        'respostas',
        'status',
        'observacoes',
        'ai_score',
        'ai_summary',
        'ai_pontos_fortes',
        'ai_pontos_fracos',
        'ai_status',
    ];


    protected $casts = [
        'respostas' => 'array',
    ];

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }

    /**
     * Gera link direto para o WhatsApp do candidato.
     */
    public function linkWhatsapp(?string $mensagem = null): string
    {
        $numero = preg_replace('/\D/', '', $this->telefone);

        // Garante formato internacional (Brasil)
        if (strlen($numero) <= 11) {
            $numero = '55' . $numero;
        }

        $url = "https://wa.me/{$numero}";
        if ($mensagem) {
            $url .= '?text=' . urlencode($mensagem);
        }

        return $url;
    }
}
