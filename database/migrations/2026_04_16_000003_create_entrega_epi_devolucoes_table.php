<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_epi_devolucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_epi_id')->constrained('entregas_epi')->cascadeOnDelete();
            $table->foreignId('entrega_origem_id')->nullable()->constrained('entregas_epi')->nullOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('data_devolucao');
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_epi_devolucoes');
    }
};