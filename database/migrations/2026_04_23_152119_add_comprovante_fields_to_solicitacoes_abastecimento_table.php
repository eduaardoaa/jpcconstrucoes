<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitacoes_abastecimento', function (Blueprint $table) {
            $table->string('foto_nota')->nullable()->after('foto_painel');
            $table->string('foto_selfie')->nullable()->after('foto_nota');
            $table->timestamp('comprovante_enviado_em')->nullable()->after('foto_selfie');
            $table->string('status_comprovante', 30)->nullable()->after('comprovante_enviado_em');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacoes_abastecimento', function (Blueprint $table) {
            $table->dropColumn([
                'foto_nota',
                'foto_selfie',
                'comprovante_enviado_em',
                'status_comprovante',
            ]);
        });
    }
};