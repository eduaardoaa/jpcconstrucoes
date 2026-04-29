<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pode_ter_veiculo')) {
                $table->boolean('pode_ter_veiculo')->default(false)->after('primeiro_acesso');
            }

            if (!Schema::hasColumn('users', 'veiculo_id')) {
                $table->foreignId('veiculo_id')
                    ->nullable()
                    ->unique()
                    ->after('pode_ter_veiculo')
                    ->constrained('veiculos')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'veiculo_id')) {
                $table->dropForeign(['veiculo_id']);
                $table->dropUnique(['veiculo_id']);
                $table->dropColumn('veiculo_id');
            }

            if (Schema::hasColumn('users', 'pode_ter_veiculo')) {
                $table->dropColumn('pode_ter_veiculo');
            }
        });
    }
};