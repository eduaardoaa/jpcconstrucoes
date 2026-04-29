<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_mensagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_instancia_id')
                ->constrained('whatsapp_instancias')
                ->cascadeOnDelete();

            $table->foreignId('whatsapp_conversa_id')
                ->constrained('whatsapp_conversas')
                ->cascadeOnDelete();

            $table->foreignId('whatsapp_contato_id')
                ->constrained('whatsapp_contatos')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('message_id')->nullable();

            $table->enum('direcao', ['entrada', 'saida'])->default('entrada');

            $table->enum('tipo', [
                'texto',
                'imagem',
                'audio',
                'video',
                'documento',
                'figurinha',
                'localizacao',
                'contato',
                'outro'
            ])->default('texto');

            $table->longText('conteudo')->nullable();

            $table->json('payload')->nullable();

            $table->enum('status_envio', [
                'pendente',
                'enviada',
                'entregue',
                'lida',
                'falha'
            ])->nullable();

            $table->timestamp('enviada_em')->nullable();

            $table->timestamps();

            $table->index(['whatsapp_conversa_id', 'created_at']);

            $table->unique(
                ['whatsapp_instancia_id', 'message_id'],
                'wa_msg_instancia_message_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_mensagens');
    }
};