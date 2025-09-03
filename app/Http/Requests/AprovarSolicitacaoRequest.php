<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="AprovarSolicitacaoRequest",
 *     type="object",
 *     @OA\Property(property="observacoes", type="string", example="Aprovado pelo gestor"),
 *     @OA\Property(property="motivo_rejeicao", type="string", example="Documento incompleto")
 * )
 */
class AprovarSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'observacoes' => 'nullable|string|max:1000',
            'motivo_rejeicao' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'observacoes.max' => 'Observações não podem exceder 1000 caracteres',
            'motivo_rejeicao.max' => 'Motivo da rejeição não pode exceder 1000 caracteres'
        ];
    }
}
