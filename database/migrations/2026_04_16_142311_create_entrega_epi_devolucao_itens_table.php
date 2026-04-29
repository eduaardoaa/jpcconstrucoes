<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_epi_devolucao_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_epi_devolucao_id')
                ->constrained('entrega_epi_devolucoes')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->foreignId('produto_variacao_id')->nullable()->constrained('produto_variacoes')->nullOnDelete();
            $table->unsignedInteger('quantidade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_epi_devolucao_itens');
    }
};