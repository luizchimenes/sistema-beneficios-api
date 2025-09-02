<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $usuario = JWTAuth::parseToken()->authenticate();
            
            if (!$usuario) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não encontrado'
                ], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Token expirado'
            ], 401);
            
        } catch (TokenInvalidException $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Token inválido'
            ], 401);
            
        } catch (JWTException $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Token ausente'
            ], 401);
        }

        return $next($request);
    }
}