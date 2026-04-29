<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_instancia_id')
                ->constrained('whatsapp_instancias')
                ->cascadeOnDelete();

            $table->foreignId('whatsapp_contato_id')
                ->constrained('whatsapp_contatos')
                ->cascadeOnDelete();

            $table->foreignId('atendente_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['aberta', 'fechada'])->default('aberta');

            $table->integer('nao_lidas')->default(0);

            $table->timestamp('ultima_mensagem_em')->nullable();
            $table->text('ultima_mensagem_preview')->nullable();

            $table->timestamps();

            $table->unique(
                ['whatsapp_instancia_id', 'whatsapp_contato_id'],
                'wa_conversa_instancia_contato_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversas');
    }
};