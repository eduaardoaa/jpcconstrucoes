<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_to_message_id')->nullable()->after('participant');
            $table->foreign('reply_to_message_id')
                ->references('id')
                ->on('whatsapp_mensagens')
                ->nullOnDelete();

            $table->timestamp('apagada_em')->nullable()->after('enviada_em');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_mensagens', function (Blueprint $table) {
            $table->dropForeign(['reply_to_message_id']);
            $table->dropColumn(['reply_to_message_id', 'apagada_em']);
        });
    }
};
