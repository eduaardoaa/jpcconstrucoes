<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obra_treinamento_dds_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_treinamento_dds_id')
                ->constrained('obra_treinamentos_dds')
                ->cascadeOnDelete();

            $table->string('arquivo');
            $table->string('nome_original')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obra_treinamento_dds_anexos');
    }
};