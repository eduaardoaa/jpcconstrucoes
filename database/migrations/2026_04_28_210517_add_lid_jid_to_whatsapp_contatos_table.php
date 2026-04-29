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
        Schema::table('whatsapp_contatos', function (Blueprint $table) {
            $table->string('lid_jid')->nullable()->after('remote_jid')
                ->comment('JID @lid original. Mantido mesmo após remote_jid ser atualizado para o número real.');
            $table->index(['whatsapp_instancia_id', 'lid_jid'], 'wa_contatos_instancia_lid_idx');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_contatos', function (Blueprint $table) {
            $table->dropIndex('wa_contatos_instancia_lid_idx');
            $table->dropColumn('lid_jid');
        });
    }
};
