<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="SolicitarBeneficioRequest",
 *     type="object",
 *     required={"beneficio_id","valor_solicitado"},
 *     @OA\Property(property="beneficio_id", type="integer", example=2),
 *     @OA\Property(property="valor_solicitado", type="number", format="float", example=150.50),
 *     @OA\Property(property="justificativa", type="string", example="Preciso deste benefício para alimentação")
 * )
 */
class SolicitarBeneficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beneficio_id' => 'required|exists:beneficios,id',
            'valor_solicitado' => 'required|numeric|min:0.01',
            'justificativa' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'beneficio_id.required' => 'Selecione um benefício',
            'beneficio_id.exists' => 'Benefício selecionado não existe',
            'valor_solicitado.required' => 'Valor solicitado é obrigatório',
            'valor_solicitado.numeric' => 'Valor deve ser numérico',
            'valor_solicitado.min' => 'Valor deve ser maior que zero',
            'justificativa.max' => 'Justificativa não pode exceder 1000 caracteres'
        ];
    }
}