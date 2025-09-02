<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beneficio;
use App\Enums\TipoBeneficio;

class BeneficioSeeder extends Seeder
{
    public function run(): void
    {
        Beneficio::create([
            'nome' => 'Vale Alimentação Swile',
            'tipo' => TipoBeneficio::VALE_ALIMENTACAO,
            'descricao' => 'Auxílio para alimentação do colaborador',
            'valor_maximo' => 500.00,
            'regras' => [
                [
                    'tipo' => 'valor_minimo',
                    'valor' => 100.00,
                    'descricao' => 'Valor mínimo de R$ 100,00'
                ]
            ],
            'ativo' => true,
            'requer_aprovacao_dupla' => false
        ]);

        Beneficio::create([
            'nome' => 'Vale Combustível',
            'tipo' => TipoBeneficio::VALE_COMBUSTIVEL,
            'descricao' => 'Auxílio para combustível do colaborador',
            'valor_maximo' => 300.00,
            'regras' => [
                [
                    'tipo' => 'justificativa_obrigatoria',
                    'descricao' => 'Justificativa obrigatória'
                ]
            ],
            'ativo' => true,
            'requer_aprovacao_dupla' => true
        ]);

        Beneficio::create([
            'nome' => 'Totalpass',
            'tipo' => TipoBeneficio::CONVENIO_ACADEMIA,
            'descricao' => 'Desconto em academias parceiras',
            'valor_maximo' => 150.00,
            'ativo' => true,
            'requer_aprovacao_dupla' => false
        ]);

        Beneficio::create([
            'nome' => 'Cartão Presente',
            'tipo' => TipoBeneficio::CARTAO_PRESENTE,
            'descricao' => 'Cartão presente em lojas parceiras',
            'valor_maximo' => 200.00,
            'regras' => [
                [
                    'tipo' => 'departamento_restrito',
                    'departamentos' => ['Vendas', 'Marketing'],
                    'descricao' => 'Disponível apenas para Vendas e Marketing'
                ]
            ],
            'ativo' => true,
            'requer_aprovacao_dupla' => true
        ]);
    }
}