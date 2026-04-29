<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_whatsapp_instancia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('whatsapp_instancia_id')
                ->constrained('whatsapp_instancias')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'whatsapp_instancia_id'], 'user_whatsapp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_whatsapp_instancia');
    }
};