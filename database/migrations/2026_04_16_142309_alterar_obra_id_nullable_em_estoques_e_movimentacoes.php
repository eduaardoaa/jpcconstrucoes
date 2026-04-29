<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('estoques')) {
            DB::statement('ALTER TABLE estoques MODIFY obra_id BIGINT UNSIGNED NULL');
        }

        if (Schema::hasTable('movimentacoes_estoque')) {
            DB::statement('ALTER TABLE movimentacoes_estoque MODIFY obra_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('estoques')) {
            DB::statement('ALTER TABLE estoques MODIFY obra_id BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasTable('movimentacoes_estoque')) {
            DB::statement('ALTER TABLE movimentacoes_estoque MODIFY obra_id BIGINT UNSIGNED NOT NULL');
        }
    }
};