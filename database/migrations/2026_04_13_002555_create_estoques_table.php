<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->foreignId('produto_variacao_id')->nullable()->constrained('produto_variacoes')->nullOnDelete();
            $table->decimal('quantidade_atual', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['obra_id', 'produto_id', 'produto_variacao_id'],
                'estoques_unique_item'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};