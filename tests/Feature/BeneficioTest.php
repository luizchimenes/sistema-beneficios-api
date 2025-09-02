<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Beneficio;
use App\Enums\TipoUsuario;
use App\Enums\TipoBeneficio;
use Tymon\JWTAuth\Facades\JWTAuth;

class BeneficioTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['auth.defaults.guard' => 'api']);
        
        $this->usuario = Usuario::factory()->create([
            'tipo' => TipoUsuario::FUNCIONARIO
        ]);
        
        $this->token = JWTAuth::fromUser($this->usuario);
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
            'valor_maximo' => 500.00,
            'ativo' => true
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->getJson("/api/beneficios/{$beneficio->id}");

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'nome' => 'Vale Teste',
                    'valor_maximo_raw' => '500.00'
                ]);
    }

    public function test_usuario_nao_autenticado_nao_pode_listar_beneficios(): void
    {
        $response = $this->getJson('/api/beneficios');

        $response->assertStatus(401);
    }

    public function test_beneficio_inativo_nao_aparece_na_listagem(): void
    {
        Beneficio::factory()->create(['ativo' => true]);
        Beneficio::factory()->create(['ativo' => false]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->getJson('/api/beneficios');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}