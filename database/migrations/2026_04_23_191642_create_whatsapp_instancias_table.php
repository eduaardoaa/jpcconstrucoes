<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_instancias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('instance_name')->unique();
            $table->string('api_url')->nullable();
            $table->text('api_key')->nullable();
            $table->string('webhook_token')->unique();
            $table->enum('status', ['ativa', 'inativa'])->default('ativa');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instancias');
    }
};