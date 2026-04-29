<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->foreignId('produto_variacao_id')->nullable()->constrained('produto_variacoes')->nullOnDelete();
            $table->enum('tipo_movimentacao', ['entrada', 'saida', 'ajuste']);
            $table->decimal('quantidade', 12, 2);
            $table->decimal('quantidade_anterior', 12, 2)->default(0);
            $table->decimal('quantidade_posterior', 12, 2)->default(0);
            $table->text('observacao')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('data_movimentacao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_estoque');
    }
};