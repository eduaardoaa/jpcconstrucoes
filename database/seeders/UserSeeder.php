<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cargoAdmin = Cargo::where('nome', 'Administrador')->first();

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'cpf' => '061.375.065-92',
                'telefone' => '(79) 99999-9999',
                'cargo_id' => $cargoAdmin?->id,
                'password' => Hash::make('12345'),
                'status' => 'ativo',
                'primeiro_acesso' => true,
            ]
        );
    }
}