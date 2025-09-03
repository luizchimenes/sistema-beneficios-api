<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credenciais = [
            'email' => $request->email,
            'password' => $request->senha
        ];

        if (!$token = JWTAuth::attempt($credenciais)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Credenciais inválidas'
            ], 401);
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Login realizado com sucesso',
            'dados' => [
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
            ]
        ]);
    }

    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Logout realizado com sucesso'
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'sucesso' => true,
            'dados' => [
                'token' => $token,
                'tipo_token' => 'bearer',
                'expira_em' => JWTAuth::factory()->getTTL() * 60
            ]
        ]);
    }

    public function perfil(): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return response()->json([
            'sucesso' => true,
            'dados' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo->value,
                'departamento' => $usuario->departamento
            ]
        ]);
    }
}