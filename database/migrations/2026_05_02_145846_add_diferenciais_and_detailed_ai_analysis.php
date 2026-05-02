<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->text('diferenciais')->nullable()->after('requisitos');
        });

        Schema::table('vaga_candidaturas', function (Blueprint $table) {
            $table->text('ai_pontos_fortes')->nullable()->after('ai_summary');
            $table->text('ai_pontos_fracos')->nullable()->after('ai_pontos_fortes');
        });
    }

    public function down(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->dropColumn('diferenciais');
        });

        Schema::table('vaga_candidaturas', function (Blueprint $table) {
            $table->dropColumn(['ai_pontos_fortes', 'ai_pontos_fracos']);
        });
    }

};
