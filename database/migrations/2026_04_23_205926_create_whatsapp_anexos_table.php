<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_anexos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whatsapp_mensagem_id')
                ->constrained('whatsapp_mensagens')
                ->cascadeOnDelete();

            $table->string('tipo')->nullable();
            $table->string('nome_arquivo')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();

            $table->text('url_original')->nullable();
            $table->string('caminho_local')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_anexos');
    }
};