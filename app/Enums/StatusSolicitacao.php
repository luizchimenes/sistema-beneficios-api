<?php 

namespace App\Enums;

enum StatusSolicitacao: string 
{
    case PENDENTE = 'pendente';
    case APROVADA = 'aprovada';
    case REJEITADA = 'rejeitada';
    case APROVACAO_DUPLA_PENDENTE = 'aprovacao_dupla_pendente';

    public function getLabel(): string {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::APROVADA => 'Aprovada',
            self::REJEITADA => 'Rejeitada',
            self::APROVACAO_DUPLA_PENDENTE => 'Aguardanddo Segunda Aprovacao'
        };
    }
}