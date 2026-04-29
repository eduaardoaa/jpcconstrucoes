<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Permissao;
use Illuminate\Database\Seeder;

class CargoPermissaoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Cargo::where('nome', 'Administrador')->first();
        $engenheiro = Cargo::where('nome', 'Engenheiro')->first();
        $tecnico = Cargo::where('nome', 'Técnico de Segurança')->first();

        $todas = Permissao::pluck('id')->toArray();

        if ($admin) {
            $admin->permissoes()->sync($todas);
        }

        if ($engenheiro) {
            $engenheiro->permissoes()->sync(
                Permissao::whereIn('chave', ['obras', 'funcionarios', 'estoque', 'entregas_epi'])
                    ->pluck('id')
                    ->toArray()
            );
        }

        if ($tecnico) {
            $tecnico->permissoes()->sync(
                Permissao::whereIn('chave', ['obras', 'entregas_epi', 'relatorios'])
                    ->pluck('id')
                    ->toArray()
            );
        }
    }
}