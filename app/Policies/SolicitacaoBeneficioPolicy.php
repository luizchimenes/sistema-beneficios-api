<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\SolicitacaoBeneficio;

class SolicitacaoBeneficioPolicy
{
    public function view(Usuario $usuario, SolicitacaoBeneficio $solicitacao): bool
    {
        return $usuario->id === $solicitacao->usuario_id || $usuario->podeAprovar();
    }

    public function approve(Usuario $usuario, SolicitacaoBeneficio $solicitacao): bool
    {
        return $usuario->podeAprovar() && $usuario->id !== $solicitacao->usuario_id;
    }

    public function reject(Usuario $usuario, SolicitacaoBeneficio $solicitacao): bool
    {
        return $usuario->podeAprovar() && $usuario->id !== $solicitacao->usuario_id;
    }
}