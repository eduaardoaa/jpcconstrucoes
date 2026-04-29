<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObraTreinamentoDdsAnexo extends Model
{
    protected $table = 'obra_treinamento_dds_anexos';

    protected $fillable = [
        'obra_treinamento_dds_id',
        'arquivo',
        'nome_original',
        'mime_type',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(ObraTreinamentoDds::class, 'obra_treinamento_dds_id');
    }
}