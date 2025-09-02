<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeneficioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'tipo' => [
                'valor' => $this->tipo->value,
                'label' => $this->tipo->label()
            ],
            'descricao' => $this->descricao,
            'valor_maximo' => $this->valor_maximo ? number_format($this->valor_maximo, 2, ',', '.') : null,
            'valor_maximo_raw' => $this->valor_maximo,
            'requer_aprovacao_dupla' => $this->requer_aprovacao_dupla,
            'ativo' => $this->ativo,
            'regras' => $this->regras,
            'criado_em' => $this->created_at->format('d/m/Y H:i:s'),
            'atualizado_em' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}