<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas_epi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')
                ->constrained('funcionarios')
                ->cascadeOnDelete();

            $table->foreignId('obra_id')
                ->constrained('obras')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->cascadeOnDelete();

            $table->foreignId('produto_variacao_id')
                ->nullable()
                ->constrained('produto_variacoes')
                ->nullOnDelete();

            $table->integer('quantidade');

            $table->date('data_entrega');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas_epi');
    }
};