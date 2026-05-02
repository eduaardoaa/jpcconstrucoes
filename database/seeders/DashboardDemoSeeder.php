<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obra;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use App\Models\Funcionario;
use App\Models\Cargo;
use App\Models\Estoque;
use App\Models\EntregaEpi;
use App\Models\EntregaEpiItem;
use App\Models\User;
use Carbon\Carbon;

class DashboardDemoSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 1. Obras
        $obras = [
            ['nome' => 'Residencial Aurora', 'status' => 'ativa'],
            ['nome' => 'Shopping Central', 'status' => 'ativa'],
            ['nome' => 'Edifício Horizonte', 'status' => 'ativa'],
            ['nome' => 'Ponte Rio das Almas', 'status' => 'ativa'],
        ];

        $obraModels = [];
        foreach ($obras as $o) {
            $obraModels[] = Obra::firstOrCreate(['nome' => $o['nome']], $o);
        }

        // 2. Cargos
        $cargos = ['Pedreiro', 'Servente', 'Mestre de Obras', 'Eletricista', 'Encanador'];
        $cargoModels = [];
        foreach ($cargos as $c) {
            $cargoModels[] = Cargo::firstOrCreate(['nome' => $c]);
        }

        // 3. Funcionários
        $nomes = ['João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Souza', 'Lucas Pereira', 'Carla Lima', 'Bruno Rocha', 'Fernanda Costa'];
        foreach ($nomes as $index => $nome) {
            Funcionario::firstOrCreate(
                ['cpf' => '000.000.000-' . str_pad($index, 2, '0', STR_PAD_LEFT)],
                [
                    'nome' => $nome,
                    'obra_id' => $obraModels[array_rand($obraModels)]->id,
                    'cargo_id' => $cargoModels[array_rand($cargoModels)]->id,
                    'matricula' => 'MAT-' . (1000 + $index),
                    'status' => 'ativo',
                    'data_admissao' => now()->subMonths(rand(1, 12)),
                ]
            );
        }
        $funcionarios = Funcionario::all();

        // 4. Produtos & Variações
        $produtos = [
            ['nome' => 'Bota de Segurança', 'status' => 'ativo', 'unidade' => 'PAR', 'descricao' => 'Bota com bico de aço'],
            ['nome' => 'Capacete de Proteção', 'status' => 'ativo', 'unidade' => 'UN', 'descricao' => 'Capacete aba frontal'],
            ['nome' => 'Luva de Vaqueta', 'status' => 'ativo', 'unidade' => 'PAR', 'descricao' => 'Luva para proteção mecânica'],
            ['nome' => 'Óculos de Proteção', 'status' => 'ativo', 'unidade' => 'UN', 'descricao' => 'Lente incolor'],
            ['nome' => 'Protetor Auricular', 'status' => 'ativo', 'unidade' => 'UN', 'descricao' => 'Tipo plug'],
        ];

        $variacoes = ['P', 'M', 'G', 'GG', 'Tamanho 40', 'Tamanho 41', 'Tamanho 42'];

        foreach ($produtos as $p) {
            $prod = Produto::firstOrCreate(['nome' => $p['nome']], $p);
            
            // Adicionar variações para alguns
            if (in_array($prod->nome, ['Bota de Segurança', 'Luva de Vaqueta'])) {
                foreach (array_slice($variacoes, rand(0, 3), 3) as $v) {
                    ProdutoVariacao::firstOrCreate(
                        ['produto_id' => $prod->id, 'nome_variacao' => $v]
                    );
                }
            }
        }

        $prodModels = Produto::with('variacoes')->get();

        // 5. Estoque
        foreach ($obraModels as $obra) {
            foreach ($prodModels as $prod) {
                if ($prod->variacoes->count() > 0) {
                    foreach ($prod->variacoes as $var) {
                        Estoque::updateOrCreate(
                            ['obra_id' => $obra->id, 'produto_id' => $prod->id, 'produto_variacao_id' => $var->id],
                            ['quantidade_atual' => rand(0, 50)]
                        );
                    }
                } else {
                    Estoque::updateOrCreate(
                        ['obra_id' => $obra->id, 'produto_id' => $prod->id, 'produto_variacao_id' => null],
                        ['quantidade_atual' => rand(0, 50)]
                    );
                }
            }
        }

        // 6. Entregas (últimos 40 dias)
        for ($i = 0; $i < 50; $i++) {
            $data = now()->subDays(rand(0, 40));
            $func = $funcionarios->random();
            $obra = $func->obra; // Usar a obra do funcionário para fazer sentido

            $entrega = EntregaEpi::create([
                'funcionario_id' => $func->id,
                'obra_id' => $obra->id,
                'user_id' => $user->id,
                'data_entrega' => $data->toDateString(),
                'status_comprovante' => rand(0, 1) ? 'anexado' : 'pendente',
            ]);

            // Adicionar itens à entrega
            $numItens = rand(1, 3);
            for ($j = 0; $j < $numItens; $j++) {
                $prod = $prodModels->random();
                $var = $prod->variacoes->count() > 0 ? $prod->variacoes->random() : null;
                
                EntregaEpiItem::create([
                    'entrega_epi_id' => $entrega->id,
                    'produto_id' => $prod->id,
                    'produto_variacao_id' => $var ? $var->id : null,
                    'quantidade' => rand(1, 5),
                ]);
            }
        }
    }
}
