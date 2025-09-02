<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Beneficio;
use App\Models\SolicitacaoBeneficio;
use App\Enums\TipoUsuario;
use App\Enums\StatusSolicitacao;
use Tymon\JWTAuth\Facades\JWTAuth;

class SolicitacaoBeneficioTest extends TestCase
{
    use RefreshDatabase;

    protected $funcionario;
    protected $aprovador;
    protected $beneficio;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'api']);

        $this->funcionario = Usuario::factory()->create([
            'tipo' => TipoUsuario::FUNCIONARIO,
            'departamento' => 'TI'
        ]);

        $this->aprovador = Usuario::factory()->create([
            'tipo' => TipoUsuario::APROVADOR,
            'departamento' => 'RH'
        ]);

        $this->beneficio = Beneficio::factory()->create([
            'valor_maximo' => 500.00,
            'ativo' => true,
            'requer_aprovacao_dupla' => false
        ]);
    }

    public function test_funcionario_pode_criar_solicitacao(): void
    {
        $token = JWTAuth::fromUser($this->funcionario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/solicitacoes', [
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 300.00,
            'justificativa' => 'Necessário para trabalho'
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'sucesso' => true,
                'mensagem' => 'Solicitação criada com sucesso'
            ]);

        $this->assertDatabaseHas('solicitacoes_beneficios', [
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 300.00,
            'status' => StatusSolicitacao::PENDENTE->value
        ]);
    }

    public function test_nao_pode_solicitar_valor_acima_do_maximo(): void
    {
        $token = JWTAuth::fromUser($this->funcionario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/solicitacoes', [
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 600.00, // Acima do máximo
            'justificativa' => 'Teste'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'sucesso' => false
            ]);
    }

    public function test_aprovador_pode_aprovar_solicitacao(): void
    {
        $solicitacao = SolicitacaoBeneficio::create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 300.00,
            'status' => StatusSolicitacao::PENDENTE->value
        ]);

        $token = JWTAuth::fromUser($this->aprovador);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/aprovar", [
            'observacoes' => 'Aprovado pelo gestor'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('solicitacoes_beneficios', [
            'id' => $solicitacao->id,
            'status' => StatusSolicitacao::APROVADA->value,
            'aprovado_por' => $this->aprovador->id
        ]);
    }

    public function test_funcionario_nao_pode_aprovar_propria_solicitacao(): void
    {
        $solicitacao = SolicitacaoBeneficio::create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 300.00,
            'status' => StatusSolicitacao::PENDENTE->value
        ]);

        $token = JWTAuth::fromUser($this->funcionario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/aprovar");

        $response->assertStatus(403);
    }

    public function test_aprovador_pode_rejeitar_solicitacao(): void
    {
        $solicitacao = SolicitacaoBeneficio::create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id,
            'valor_solicitado' => 300.00,
            'status' => StatusSolicitacao::PENDENTE->value
        ]);

        $token = JWTAuth::fromUser($this->aprovador);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/rejeitar", [
            'motivo_rejeicao' => 'Valor muito alto'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('solicitacoes_beneficios', [
            'id' => $solicitacao->id,
            'status' => StatusSolicitacao::REJEITADA->value,
            'motivo_rejeicao' => 'Valor muito alto'
        ]);
    }

    public function test_funcionario_pode_listar_suas_solicitacoes(): void
    {
        SolicitacaoBeneficio::factory()->count(2)->create([
            'usuario_id' => $this->funcionario->id
        ]);

        $outroUsuario = Usuario::factory()->create();
        SolicitacaoBeneficio::factory()->create([
            'usuario_id' => $outroUsuario->id
        ]);

        $token = JWTAuth::fromUser($this->funcionario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/solicitacoes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_aprovacao_dupla_requer_dois_aprovadores(): void
    {
        $beneficioDupla = Beneficio::factory()->create([
            'requer_aprovacao_dupla' => true,
            'ativo' => true
        ]);

        $solicitacao = SolicitacaoBeneficio::create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $beneficioDupla->id,
            'valor_solicitado' => 300.00,
            'status' => StatusSolicitacao::PENDENTE->value
        ]);

        $segundoAprovador = Usuario::factory()->create([
            'tipo' => TipoUsuario::APROVADOR
        ]);

        $token1 = JWTAuth::fromUser($this->aprovador);

        $response1 = $this->withHeaders([
            'Authorization' => "Bearer $token1"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/aprovar");

        $response1->assertStatus(200);

        $this->assertDatabaseHas('solicitacoes_beneficios', [
            'id' => $solicitacao->id,
            'status' => StatusSolicitacao::APROVACAO_DUPLA_PENDENTE->value
        ]);

        $token2 = JWTAuth::fromUser($segundoAprovador);

        $response2 = $this->withHeaders([
            'Authorization' => "Bearer $token2"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/aprovar");

        $response2->assertStatus(200);

        $this->assertDatabaseHas('solicitacoes_beneficios', [
            'id' => $solicitacao->id,
            'status' => StatusSolicitacao::APROVADA->value,
            'segunda_aprovacao_por' => $segundoAprovador->id
        ]);
    }
}
