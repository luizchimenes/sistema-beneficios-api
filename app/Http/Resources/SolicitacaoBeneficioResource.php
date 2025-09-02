<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitacaoBeneficioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'valor_solicitado' => number_format($this->valor_solicitado, 2, ',', '.'),
            'valor_solicitado_raw' => $this->valor_solicitado,
            'justificativa' => $this->justificativa,
            'status' => [
                'valor' => $this->status->value,
                'label' => $this->status->label()
            ],
            'motivo_rejeicao' => $this->motivo_rejeicao,
            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario->id,
                    'nome' => $this->usuario->nome,
                    'email' => $this->usuario->email,
                    'departamento' => $this->usuario->departamento
                ];
            }),
            'beneficio' => $this->whenLoaded('beneficio', function () {
                return new BeneficioResource($this->beneficio);
            }),
            'aprovador' => $this->whenLoaded('aprovador', function () {
                return $this->aprovador ? [
                    'id' => $this->aprovador->id,
                    'nome' => $this->aprovador->nome,
                    'email' => $this->aprovador->email
                ] : null;
            }),
            'segundo_aprovador' => $this->whenLoaded('segundoAprovador', function () {
                return $this->segundoAprovador ? [
                    'id' => $this->segundoAprovador->id,
                    'nome' => $this->segundoAprovador->nome,
                    'email' => $this->segundoAprovador->email
                ] : null;
            }),
            'aprovado_em' => $this->aprovado_em?->format('d/m/Y H:i:s'),
            'segunda_aprovacao_em' => $this->segunda_aprovacao_em?->format('d/m/Y H:i:s'),
            'criado_em' => $this->created_at->format('d/m/Y H:i:s'),
            'atualizado_em' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}