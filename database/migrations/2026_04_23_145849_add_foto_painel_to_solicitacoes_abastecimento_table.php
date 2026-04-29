<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitacoes_abastecimento', function (Blueprint $table) {
            $table->string('foto_painel')->after('km_informado');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacoes_abastecimento', function (Blueprint $table) {
            $table->dropColumn('foto_painel');
        });
    }
};