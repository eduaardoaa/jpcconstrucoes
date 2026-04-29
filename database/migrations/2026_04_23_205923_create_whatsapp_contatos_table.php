<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_contatos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_instancia_id')
                ->constrained('whatsapp_instancias')
                ->cascadeOnDelete();

            $table->string('remote_jid');
            $table->string('numero')->nullable();
            $table->string('nome')->nullable();
            $table->string('push_name')->nullable();
            $table->string('foto_url')->nullable();
            $table->boolean('is_grupo')->default(false);

            $table->timestamps();

            $table->unique(
                ['whatsapp_instancia_id', 'remote_jid'],
                'wa_contato_instancia_jid_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_contatos');
    }
};