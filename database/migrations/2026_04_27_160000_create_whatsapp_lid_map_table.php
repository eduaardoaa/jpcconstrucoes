<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_lid_map', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_instancia_id')
                ->constrained('whatsapp_instancias')
                ->cascadeOnDelete();

            // O LID completo (ex: 123456789@lid)
            $table->string('lid');

            // O número real (ex: 5511999999999)
            $table->string('numero');

            // O JID real (ex: 5511999999999@s.whatsapp.net)
            $table->string('jid_real');

            // Push name para referência
            $table->string('push_name')->nullable();

            $table->timestamps();

            $table->unique(
                ['whatsapp_instancia_id', 'lid'],
                'wa_lid_map_unique'
            );

            $table->index(
                ['whatsapp_instancia_id', 'numero'],
                'wa_lid_map_numero_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_lid_map');
    }
};
