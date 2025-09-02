<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Enums\TipoUsuario;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['auth.defaults.guard' => 'api']);
    }

    public function test_usuario_pode_fazer_login(): void
    {
        $usuario = Usuario::create([
            'nome' => 'Teste Usuario',
            'email' => 'teste@empresa.com',
            'senha' => Hash::make('123456'),
            'tipo' => TipoUsuario::FUNCIONARIO,
            'departamento' => 'TI'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@empresa.com',
            'senha' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'sucesso',
                    'mensagem',
                    'dados' => [
                        'token',
                        'tipo_token',
                        'expira_em',
                        'usuario' => [
                            'id',
                            'nome',
                            'email',
                            'tipo',
                            'departamento'
                        ]
                    ]
                ]);
    }

    public function test_login_com_credenciais_invalidas(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'inexistente@empresa.com',
            'senha' => 'senhaerrada'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'sucesso' => false,
                    'mensagem' => 'Credenciais inválidas'
                ]);
    }

    public function test_usuario_pode_fazer_logout(): void
    {
        $usuario = Usuario::factory()->create();
        
        $token = JWTAuth::fromUser($usuario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'sucesso' => true,
                    'mensagem' => 'Logout realizado com sucesso'
                ]);
    }

    public function test_usuario_pode_acessar_perfil(): void
    {
        $usuario = Usuario::factory()->create([
            'nome' => 'Teste Perfil',
            'email' => 'perfil@empresa.com'
        ]);
        
        $token = JWTAuth::fromUser($usuario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/auth/perfil');

        $response->assertStatus(200)
                ->assertJson([
                    'sucesso' => true,
                    'dados' => [
                        'nome' => 'Teste Perfil',
                        'email' => 'perfil@empresa.com'
                    ]
                ]);
    }

    public function test_acesso_sem_token_retorna_401(): void
    {
        $response = $this->getJson('/api/auth/perfil');

        $response->assertStatus(401)
                ->assertJson([
                    'sucesso' => false,
                    'mensagem' => 'Token ausente'
                ]);
    }
}