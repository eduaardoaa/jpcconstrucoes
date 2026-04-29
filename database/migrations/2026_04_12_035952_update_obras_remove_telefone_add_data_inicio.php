<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn('telefone');
            $table->date('data_inicio')->nullable()->after('responsavel');
        });
    }

    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->string('telefone', 20)->nullable()->after('responsavel');
            $table->dropColumn('data_inicio');
        });
    }
};