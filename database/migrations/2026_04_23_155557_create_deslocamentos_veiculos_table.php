<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deslocamentos_veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->string('motivo')->nullable();
            $table->text('observacao')->nullable();
            $table->enum('status', ['em_andamento', 'finalizado'])->default('em_andamento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deslocamentos_veiculos');
    }
};