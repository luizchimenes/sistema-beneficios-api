<?php

namespace App\Enums;

enum TipoUsuario: string
{
    case FUNCIONARIO = 'funcionario';
    case APROVADOR = 'aprovador';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::FUNCIONARIO => 'Funcionário',
            self::APROVADOR => 'Aprovador',
            self::ADMIN => 'Administrador',
        };
    }
}