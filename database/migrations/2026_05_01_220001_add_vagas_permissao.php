<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insere a permissão "vagas" se ainda não existir
        $exists = DB::table('permissoes')->where('chave', 'vagas')->exists();

        if (!$exists) {
            DB::table('permissoes')->insert([
                'nome'      => 'Gerenciar vagas e currículos',
                'chave'     => 'vagas',
                'descricao' => 'Criar vagas, visualizar candidatos e gerenciar currículos',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Auto-vincula ao cargo Administrador (id=1) se existir a tabela cargo_permissao
        $permissaoId = DB::table('permissoes')->where('chave', 'vagas')->value('id');
        if ($permissaoId) {
            $jaVinculado = DB::table('cargo_permissao')
                ->where('cargo_id', 1)
                ->where('permissao_id', $permissaoId)
                ->exists();

            if (!$jaVinculado) {
                DB::table('cargo_permissao')->insert([
                    'cargo_id'    => 1,
                    'permissao_id' => $permissaoId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissaoId = DB::table('permissoes')->where('chave', 'vagas')->value('id');

        if ($permissaoId) {
            DB::table('cargo_permissao')->where('permissao_id', $permissaoId)->delete();
            DB::table('permissoes')->where('id', $permissaoId)->delete();
        }
    }
};
