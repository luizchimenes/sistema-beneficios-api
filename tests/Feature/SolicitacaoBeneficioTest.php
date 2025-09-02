<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Beneficio;
use App\Models\SolicitacaoBeneficio;
use App\Enums\TipoUsuario;
use App\Enums\TipoBeneficio;
use App\Enums\StatusSolicitacao;

class SolicitacaoBeneficioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->funcionario = Usuario::factory()->create([
            'tipo' => TipoUsuario::FUNCIONARIO
        ]);
        
        $this->aprovador = Usuario::factory()->create([
            'tipo' => TipoUsuario::APROVADOR
        ]);
        
        $this->beneficio = Beneficio::factory()->create([
            'valor_maximo' => 500.00,
            'ativo' => true
        ]);
    }

    public function test_funcionario_pode_criar_solicitacao(): void
    {
        $token = auth()->login($this->funcionario);

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
        $token = auth()->login($this->funcionario);

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
        $solicitacao = SolicitacaoBeneficio::factory()->create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id,
            'status' => StatusSolicitacao::PENDENTE
        ]);

        $token = auth()->login($this->aprovador);

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
        $solicitacao = SolicitacaoBeneficio::factory()->create([
            'usuario_id' => $this->funcionario->id,
            'beneficio_id' => $this->beneficio->id
        ]);

        $token = auth()->login($this->funcionario);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson("/api/solicitacoes/{$solicitacao->id}/aprovar");

        $response->assertStatus(403);
    }
}