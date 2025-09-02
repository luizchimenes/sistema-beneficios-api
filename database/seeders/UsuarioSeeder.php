<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Enums\TipoUsuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        Usuario::create([
            'nome' => 'Administrador do Sistema',
            'email' => 'admin@empresa.com',
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::ADMIN,
            'departamento' => 'TI'
        ]);

        Usuario::create([
            'nome' => 'Luiz Aprovador',
            'email' => 'aprovador@empresa.com',
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::APROVADOR,
            'departamento' => 'RH'
        ]);

        // Funcionários
        Usuario::create([
            'nome' => 'John Dee',
            'email' => 'johndee@empresa.com',
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::FUNCIONARIO,
            'departamento' => 'Vendas'
        ]);

        Usuario::create([
            'nome' => 'Fulano Santos',
            'email' => 'fulano@empresa.com',
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::FUNCIONARIO,
            'departamento' => 'Marketing'
        ]);
    }
}