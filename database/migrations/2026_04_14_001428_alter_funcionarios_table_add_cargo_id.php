<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            if (!Schema::hasColumn('funcionarios', 'cargo_id')) {
                $table->foreignId('cargo_id')
                    ->nullable()
                    ->after('obra_id')
                    ->constrained('cargos')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            if (Schema::hasColumn('funcionarios', 'cargo_id')) {
                $table->dropConstrainedForeignId('cargo_id');
            }
        });
    }
};