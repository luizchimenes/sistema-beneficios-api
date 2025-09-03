<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    public function login(array $credenciais): ?array
    {
        if (!$token = JWTAuth::attempt($credenciais)) {
            return null;
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return [
            'token' => $token,
            'tipo_token' => 'bearer',
            'expira_em' => JWTAuth::factory()->getTTL() * 60,
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo->value,
                'departamento' => $usuario->departamento
            ]
        ];
    }

    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (JWTException) {
            return false;
        }
    }

    public function refresh(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return [
            'token' => $token,
            'tipo_token' => 'bearer',
            'expira_em' => JWTAuth::factory()->getTTL() * 60
        ];
    }

    public function perfil(): array
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'tipo' => $usuario->tipo->value,
            'departamento' => $usuario->departamento
        ];
    }
}
