<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->string('ano', 4)->nullable();
            $table->string('cor')->nullable();
            $table->enum('tipo_combustivel', ['gasolina', 'etanol', 'diesel', 'flex', 'gnv']);
            $table->decimal('km_atual', 10, 1)->default(0);
            $table->enum('status', ['ativo', 'inativo', 'manutencao'])->default('ativo');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};