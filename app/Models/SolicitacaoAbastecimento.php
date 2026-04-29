<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoAbastecimento extends Model
{
    protected $table = 'solicitacoes_abastecimento';

    protected $fillable = [
        'user_id',
        'veiculo_id',
        'data_solicitacao',
        'km_informado',
        'foto_painel',
        'foto_nota',
        'foto_selfie',
        'comprovante_enviado_em',
        'status_comprovante',
        'tipo_solicitacao',
        'quantidade_solicitada',
        'status',
        'quantidade_aprovada',
        'aprovado_por',
        'aprovado_em',
        'observacao_usuario',
        'observacao_admin',
    ];

    protected function casts(): array
    {
        return [
            'data_solicitacao' => 'date',
            'aprovado_em' => 'datetime',
            'comprovante_enviado_em' => 'datetime',
            'km_informado' => 'decimal:1',
            'quantidade_solicitada' => 'decimal:2',
            'quantidade_aprovada' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }
}