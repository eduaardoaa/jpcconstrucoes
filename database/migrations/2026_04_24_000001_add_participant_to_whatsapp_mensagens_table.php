<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            // JID de quem enviou dentro do grupo (ex: 5511999999999@s.whatsapp.net)
            // Null para mensagens individuais
            $table->string('participant')->nullable()->after('message_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            $table->dropColumn('participant');
        });
    }
};
