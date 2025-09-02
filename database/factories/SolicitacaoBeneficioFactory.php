<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;
use App\Models\Beneficio;
use App\Enums\StatusSolicitacao;

class SolicitacaoBeneficioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'beneficio_id' => Beneficio::factory(),
            'valor_solicitado' => fake()->randomFloat(2, 50, 500),
            'justificativa' => fake()->sentence(),
            'status' => StatusSolicitacao::PENDENTE
        ];
    }
}