<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObraTreinamentoDds extends Model
{
    protected $table = 'obra_treinamentos_dds';

    protected $fillable = [
        'obra_id',
        'user_id',
        'data_treinamento',
        'observacoes',
    ];

    protected $casts = [
        'data_treinamento' => 'date',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ObraTreinamentoDdsAnexo::class, 'obra_treinamento_dds_id');
    }
}