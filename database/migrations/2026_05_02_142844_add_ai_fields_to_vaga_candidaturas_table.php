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
        Schema::table('vaga_candidaturas', function (Blueprint $table) {
            $table->integer('ai_score')->nullable()->after('observacoes');
            $table->text('ai_summary')->nullable()->after('ai_score');
            $table->string('ai_status')->default('pending')->after('ai_summary'); // pending, processing, completed, failed
        });
    }

    public function down(): void
    {
        Schema::table('vaga_candidaturas', function (Blueprint $table) {
            $table->dropColumn(['ai_score', 'ai_summary', 'ai_status']);
        });
    }

};
