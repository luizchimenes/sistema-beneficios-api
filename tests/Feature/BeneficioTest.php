<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Beneficio;
use App\Enums\TipoUsuario;
use App\Enums\TipoBeneficio;

class BeneficioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->usuario = Usuario::factory()->create([
            'tipo' => TipoUsuario::FUNCIONARIO
        ]);
        
        $this->token = auth()->login($this->usuario);
    }

    public function test_usuario_pode_listar_beneficios(): void
    {
        Beneficio::factory()->count(3)->create(['ativo' => true]);
        Beneficio::factory()->create(['ativo' => false]); 

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->getJson('/api/beneficios');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_usuario_pode_ver_detalhes_beneficio(): void
    {
        $beneficio = Beneficio::factory()->create([
            'nome' => 'Vale Teste',
            'tipo' => TipoBeneficio::VALE_ALIMENTACAO,
            'valor_maximo' => 500.00
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->getJson("/api/beneficios/{$beneficio->id}");

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'nome' => 'Vale Teste',
                    'valor_maximo_raw' => 500.00
                ]);
    }
}