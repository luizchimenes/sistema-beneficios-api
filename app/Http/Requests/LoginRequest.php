<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'senha' => 'required|string|min:6'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Email deve ter um formato válido',
            'senha.required' => 'O campo senha é obrigatório',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres'
        ];
    }
}