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
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            $table->timestamp('editada_em')->nullable()->after('apagada_em');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            $table->dropColumn('editada_em');
        });
    }
};
