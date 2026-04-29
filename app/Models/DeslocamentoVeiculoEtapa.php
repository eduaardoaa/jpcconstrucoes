<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeslocamentoVeiculoEtapa extends Model
{
    protected $table = 'deslocamentos_veiculo_etapas';

    protected $fillable = [
        'deslocamento_veiculo_id',
        'tipo_etapa',
        'ordem',
        'data_etapa',
        'hora_etapa',
        'local_etapa',
        'latitude',
        'longitude',
        'km_etapa',
        'foto_painel',
        'observacao',
    ];

    protected function casts(): array
    {
        return [
            'data_etapa' => 'date',
            'km_etapa' => 'decimal:1',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function deslocamento(): BelongsTo
    {
        return $this->belongsTo(DeslocamentoVeiculo::class, 'deslocamento_veiculo_id');
    }
}