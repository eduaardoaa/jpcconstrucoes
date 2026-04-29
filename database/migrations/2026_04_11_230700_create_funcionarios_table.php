<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();

            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('matricula')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('funcao')->nullable();
            $table->date('data_admissao')->nullable();

            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};