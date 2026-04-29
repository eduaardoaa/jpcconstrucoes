<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cargo_id')->nullable()->after('id')->constrained('cargos')->nullOnDelete();
            $table->string('cpf', 14)->nullable()->after('email');
            $table->string('matricula')->nullable()->after('cpf');
            $table->string('telefone', 20)->nullable()->after('matricula');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo')->after('telefone');
            $table->boolean('primeiro_acesso')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cargo_id');
            $table->dropColumn([
                'cpf',
                'matricula',
                'telefone',
                'status',
                'primeiro_acesso'
            ]);
        });
    }
};