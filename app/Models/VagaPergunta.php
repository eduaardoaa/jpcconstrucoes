<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VagaPergunta extends Model
{
    protected $fillable = [
        'vaga_id',
        'pergunta',
        'tipo',
        'opcoes',
        'obrigatoria',
        'ordem',
    ];

    protected $casts = [
        'opcoes'      => 'array',
        'obrigatoria' => 'boolean',
    ];

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }
}
