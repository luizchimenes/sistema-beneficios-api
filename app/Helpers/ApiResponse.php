<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(array $data = [], string $mensagem = 'Operação realizada com sucesso', int $status = 200): JsonResponse
    {
        return response()->json([
            'sucesso' => true,
            'mensagem' => $mensagem,
            'dados' => $data
        ], $status);
    }

    public static function error(string $mensagem = 'Erro', int $status = 400, array $data = []): JsonResponse
    {
        return response()->json([
            'sucesso' => false,
            'mensagem' => $mensagem,
            'dados' => $data
        ], $status);
    }
}
