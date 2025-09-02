<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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