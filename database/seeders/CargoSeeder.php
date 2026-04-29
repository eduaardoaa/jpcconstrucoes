<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        Cargo::updateOrCreate(
            ['nome' => 'Administrador'],
            [
                'descricao' => 'Acesso total ao sistema',
                'tipo' => 'usuario',
            ]
        );

        Cargo::updateOrCreate(
            ['nome' => 'Engenheiro'],
            [
                'descricao' => 'Acesso operacional ao sistema',
                'tipo' => 'usuario',
            ]
        );

        Cargo::updateOrCreate(
            ['nome' => 'Técnico de Segurança'],
            [
                'descricao' => 'Responsável técnico de segurança',
                'tipo' => 'usuario',
            ]
        );
    }
}