<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaEpiComprovante extends Model
{
    protected $table = 'entrega_epi_comprovantes';

    protected $fillable = [
        'entrega_epi_id',
        'arquivo',
        'nome_original',
        'mime_type',
    ];

    public function entrega(): BelongsTo
    {
        return $this->belongsTo(EntregaEpi::class, 'entrega_epi_id');
    }
}