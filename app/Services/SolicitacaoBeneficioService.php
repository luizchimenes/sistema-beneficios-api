<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Beneficio;
use App\Models\SolicitacaoBeneficio;
use App\Enums\StatusSolicitacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SolicitacaoBeneficioService
{
    public function criarSolicitacao(Usuario $usuario, array $dados): SolicitacaoBeneficio
    {
        $beneficio = Beneficio::findOrFail($dados['beneficio_id']);

        $this->validarRegrasNegocio($usuario, $beneficio, $dados);

        return DB::transaction(function () use ($usuario, $beneficio, $dados) {
            return SolicitacaoBeneficio::create([
                'usuario_id' => $usuario->id,
                'beneficio_id' => $beneficio->id,
                'valor_solicitado' => $dados['valor_solicitado'],
                'justificativa' => $dados['justificativa'] ?? null,
                'status' => StatusSolicitacao::PENDENTE
            ]);
        });
    }

    public function aprovarSolicitacao(
        SolicitacaoBeneficio $solicitacao, 
        Usuario $aprovador, 
        ?string $observacoes = null
    ): SolicitacaoBeneficio {
        
        if ($solicitacao->status !== StatusSolicitacao::PENDENTE) {
            throw new \Exception('Solicitação não está pendente');
        }

        return DB::transaction(function () use ($solicitacao, $aprovador, $observacoes) {
            $beneficio = $solicitacao->beneficio;

            if ($beneficio->requer_aprovacao_dupla && !$solicitacao->aprovado_por) {
                $solicitacao->update([
                    'aprovado_por' => $aprovador->id,
                    'aprovado_em' => now(),
                    'status' => StatusSolicitacao::APROVACAO_DUPLA_PENDENTE
                ]);
            } elseif ($beneficio->requer_aprovacao_dupla && $solicitacao->aprovado_por) {
                if ($solicitacao->aprovado_por === $aprovador->id) {
                    throw new \Exception('O mesmo aprovador não pode realizar ambas as aprovações');
                }

                $solicitacao->update([
                    'segunda_aprovacao_por' => $aprovador->id,
                    'segunda_aprovacao_em' => now(),
                    'status' => StatusSolicitacao::APROVADA
                ]);
            } else {
                $solicitacao->update([
                    'aprovado_por' => $aprovador->id,
                    'aprovado_em' => now(),
                    'status' => StatusSolicitacao::APROVADA
                ]);
            }

            return $solicitacao->fresh();
        });
    }

    public function rejeitarSolicitacao(
        SolicitacaoBeneficio $solicitacao, 
        Usuario $aprovador, 
        string $motivo
    ): SolicitacaoBeneficio {
        
        if (!in_array($solicitacao->status, [StatusSolicitacao::PENDENTE, StatusSolicitacao::APROVACAO_DUPLA_PENDENTE])) {
            throw new \Exception('Solicitação não pode ser rejeitada no status atual');
        }

        $solicitacao->update([
            'status' => StatusSolicitacao::REJEITADA,
            'aprovado_por' => $aprovador->id,
            'aprovado_em' => now(),
            'motivo_rejeicao' => $motivo
        ]);

        return $solicitacao;
    }

    private function validarRegrasNegocio(Usuario $usuario, Beneficio $beneficio, array $dados): void
    {
        if (!$beneficio->ativo) {
            throw new \Exception('Benefício não está disponível');
        }

        if ($beneficio->valor_maximo && $dados['valor_solicitado'] > $beneficio->valor_maximo) {
            throw new \Exception("Valor solicitado excede o máximo permitido de R$ {$beneficio->valor_maximo}");
        }

        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $solicitacaoExistente = SolicitacaoBeneficio::where('usuario_id', $usuario->id)
            ->where('beneficio_id', $beneficio->id)
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->whereIn('status', [StatusSolicitacao::PENDENTE, StatusSolicitacao::APROVADA, StatusSolicitacao::APROVACAO_DUPLA_PENDENTE])
            ->exists();

        if ($solicitacaoExistente) {
            throw new \Exception('Você já possui uma solicitação deste benefício no mês atual');
        }

        if ($beneficio->regras) {
            $this->validarRegrasEspecificas($usuario, $beneficio, $dados);
        }
    }

    private function validarRegrasEspecificas(Usuario $usuario, Beneficio $beneficio, array $dados): void
    {
        foreach ($beneficio->regras as $regra) {
            switch ($regra['tipo']) {
                case 'valor_minimo':
                    if ($dados['valor_solicitado'] < $regra['valor']) {
                        throw new \Exception("Valor mínimo para solicitação é R$ {$regra['valor']}");
                    }
                    break;
                    
                case 'departamento_restrito':
                    if (!in_array($usuario->departamento, $regra['departamentos'])) {
                        throw new \Exception('Este benefício não está disponível para seu departamento');
                    }
                    break;
                    
                case 'justificativa_obrigatoria':
                    if (empty($dados['justificativa'])) {
                        throw new \Exception('Justificativa é obrigatória para este benefício');
                    }
                    break; 
            }
        }
    }
}