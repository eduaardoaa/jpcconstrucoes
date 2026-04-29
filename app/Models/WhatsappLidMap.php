<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLidMap extends Model
{
    protected $table = 'whatsapp_lid_map';

    protected $fillable = [
        'whatsapp_instancia_id',
        'lid',
        'numero',
        'jid_real',
        'push_name',
    ];

    public function instancia(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstancia::class, 'whatsapp_instancia_id');
    }
}
