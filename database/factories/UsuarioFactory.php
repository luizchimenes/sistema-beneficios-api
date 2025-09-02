<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Enums\TipoUsuario;

class UsuarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::FUNCIONARIO,
            'departamento' => fake()->randomElement(['TI', 'RH', 'Vendas', 'Marketing', 'Financeiro']),
            'ativo' => true
        ];
    }
}