<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_epi_comprovantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_epi_id')
                ->constrained('entregas_epi')
                ->cascadeOnDelete();

            $table->string('arquivo');
            $table->string('nome_original')->nullable();
            $table->string('mime_type')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_epi_comprovantes');
    }
};