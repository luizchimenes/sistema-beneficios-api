<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Autenticação"},
     *     summary="Realiza o login do usuário",
     *     description="Autentica um usuário e retorna um token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@empresa.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=true),
     *             @OA\Property(property="mensagem", type="string", example="Login realizado com sucesso"),
     *             @OA\Property(
     *                 property="dados",
     *                 type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="tipo_token", type="string", example="bearer"),
     *                 @OA\Property(property="expira_em", type="integer", example=3600),
     *                 @OA\Property(
     *                     property="usuario",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@empresa.com"),
     *                     @OA\Property(property="tipo", type="string", example="admin"),
     *                     @OA\Property(property="departamento", type="string", example="TI")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=false),
     *             @OA\Property(property="mensagem", type="string", example="Credenciais inválidas")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credenciais = [
            'email' => $request->email,
            'password' => $request->senha
        ];

        $resultado = $this->authService->login($credenciais);

        if (!$resultado) {
            return ApiResponse::error('Credenciais inválidas', 401);
        }

        return ApiResponse::success($resultado, 'Login realizado com sucesso');
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Autenticação"},
     *     summary="Realiza logout do usuário",
     *     description="Invalida o token JWT atual",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=true),
     *             @OA\Property(property="mensagem", type="string", example="Logout realizado com sucesso")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        if (!$this->authService->logout()) {
            return ApiResponse::error('Não foi possível invalidar o token', 400);
        }

        return ApiResponse::success([], 'Logout realizado com sucesso');
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     tags={"Autenticação"},
     *     summary="Renova o token JWT",
     *     description="Retorna um novo token JWT válido",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token renovado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=true),
     *             @OA\Property(
     *                 property="dados",
     *                 type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="tipo_token", type="string", example="bearer"),
     *                 @OA\Property(property="expira_em", type="integer", example=3600)
     *             )
     *         )
     *     )
     * )
     */
    public function refresh(): JsonResponse
    {
        $resultado = $this->authService->refresh();
        return ApiResponse::success($resultado, 'Token renovado com sucesso');
    }

    /**
     * @OA\Get(
     *     path="/api/perfil",
     *     tags={"Autenticação"},
     *     summary="Retorna informações do usuário logado",
     *     description="Retorna os dados do perfil do usuário autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário retornados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=true),
     *             @OA\Property(
     *                 property="dados",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@empresa.com"),
     *                 @OA\Property(property="tipo", type="string", example="admin"),
     *                 @OA\Property(property="departamento", type="string", example="TI")
     *             )
     *         )
     *     )
     * )
     */
    public function perfil(): JsonResponse
    {
        $resultado = $this->authService->perfil();
        return ApiResponse::success($resultado, 'Dados do usuário retornados com sucesso');
    }
}
