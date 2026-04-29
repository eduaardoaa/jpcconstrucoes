<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeslocamentoVeiculo extends Model
{
    protected $table = 'deslocamentos_veiculos';

    protected $fillable = [
        'user_id',
        'veiculo_id',
        'motivo',
        'observacao',
        'status',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(DeslocamentoVeiculoEtapa::class, 'deslocamento_veiculo_id')->orderBy('ordem');
    }

    public function saida()
    {
        return $this->hasOne(DeslocamentoVeiculoEtapa::class, 'deslocamento_veiculo_id')
            ->where('tipo_etapa', 'saida');
    }

    public function chegada()
    {
        return $this->hasOne(DeslocamentoVeiculoEtapa::class, 'deslocamento_veiculo_id')
            ->where('tipo_etapa', 'chegada');
    }
}