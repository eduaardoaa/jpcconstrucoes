<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            [
                'nome' => 'Gerenciar usuários',
                'chave' => 'usuarios',
                'descricao' => 'Acessar o módulo de usuários do sistema',
            ],
            [
                'nome' => 'Gerenciar cargos',
                'chave' => 'cargos',
                'descricao' => 'Acessar o módulo de cargos',
            ],
            [
                'nome' => 'Gerenciar obras',
                'chave' => 'obras',
                'descricao' => 'Acessar o módulo de obras',
            ],
            [
                'nome' => 'Gerenciar funcionários',
                'chave' => 'funcionarios',
                'descricao' => 'Acessar o módulo de funcionários',
            ],
            [
                'nome' => 'Gerenciar estoque',
                'chave' => 'estoque',
                'descricao' => 'Acessar o módulo de estoque',
            ],
            [
                'nome' => 'Gerenciar entregas de EPI',
                'chave' => 'entregas_epi',
                'descricao' => 'Acessar o módulo de entregas de EPI',
            ],
            [
                'nome' => 'Visualizar relatórios',
                'chave' => 'relatorios',
                'descricao' => 'Acessar relatórios do sistema',
            ],
        ];

        foreach ($permissoes as $permissao) {
            Permissao::updateOrCreate(
                ['chave' => $permissao['chave']],
                $permissao
            );
        }
    }
}