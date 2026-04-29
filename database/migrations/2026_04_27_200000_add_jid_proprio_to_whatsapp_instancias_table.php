<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_instancias', function (Blueprint $table) {
            $table->string('jid_proprio')->nullable()->after('instance_name')
                ->comment('JID do próprio número da instância (ex: 5579...@s.whatsapp.net). Auto-preenchido pelo webhook.');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_instancias', function (Blueprint $table) {
            $table->dropColumn('jid_proprio');
        });
    }
};
