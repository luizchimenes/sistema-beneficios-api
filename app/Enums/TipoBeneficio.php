<?php

namespace App\Enums;

enum TipoBeneficio: string
{
    case VALE_ALIMENTACAO = 'vale_alimentacao';
    case VALE_COMBUSTIVEL = 'vale_combustivel';
    case CONVENIO_ACADEMIA = 'convenio_academia';
    case CONVENIO_SALAO = 'convenio_salao';
    case CONVENIO_CLINICA = 'convenio_clinica';
    case CARTAO_PRESENTE = 'cartao_presente';

    public function label(): string
    {
        return match($this) {
            self::VALE_ALIMENTACAO => 'Vale Alimentação',
            self::VALE_COMBUSTIVEL => 'Vale Combustível',
            self::CONVENIO_ACADEMIA => 'Convênio Academia',
            self::CONVENIO_SALAO => 'Convênio Salão de Beleza',
            self::CONVENIO_CLINICA => 'Convênio Clínica Estética',
            self::CARTAO_PRESENTE => 'Cartão Presente',
        };
    }
}