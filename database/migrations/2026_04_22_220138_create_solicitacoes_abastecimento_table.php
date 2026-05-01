<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_abastecimento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();

            $table->date('data_solicitacao');
            $table->decimal('km_informado', 10, 1);

            $table->enum('tipo_solicitacao', ['valor', 'litros']);
            $table->decimal('quantidade_solicitada', 10, 2);

            $table->enum('status', ['pendente', 'aprovada', 'reprovada', 'ajustada'])->default('pendente');

            $table->decimal('quantidade_aprovada', 10, 2)->nullable();

            $table->foreignId('aprovado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aprovado_em')->nullable();

            $table->text('observacao_usuario')->nullable();
            $table->text('observacao_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_abastecimento');
    }
};