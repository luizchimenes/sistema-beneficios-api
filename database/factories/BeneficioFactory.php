<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TipoBeneficio;

class BeneficioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->words(3, true),
            'tipo' => fake()->randomElement(TipoBeneficio::cases()),
            'descricao' => fake()->sentence(),
            'valor_maximo' => fake()->randomFloat(2, 100, 1000),
            'regras' => [],
            'ativo' => true,
            'requer_aprovacao_dupla' => fake()->boolean(30)
        ];
    }
}