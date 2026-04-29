<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            if (!Schema::hasColumn('funcionarios', 'matricula')) {
                $table->string('matricula')->after('cpf');
            }

            $table->unique('matricula');
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropUnique(['matricula']);
        });
    }
};
