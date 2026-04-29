<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deslocamentos_veiculo_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deslocamento_veiculo_id')->constrained('deslocamentos_veiculos')->cascadeOnDelete();
            $table->enum('tipo_etapa', ['saida', 'parada', 'chegada']);
            $table->unsignedInteger('ordem');
            $table->date('data_etapa');
            $table->time('hora_etapa');
            $table->string('local_etapa');
            $table->decimal('km_etapa', 10, 1);
            $table->string('foto_painel');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deslocamentos_veiculo_etapas');
    }
};