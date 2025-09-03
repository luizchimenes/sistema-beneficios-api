<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\BeneficioResource;
use App\Services\BeneficioService;
use Illuminate\Http\JsonResponse;

class BeneficioController extends Controller
{

    public function __construct(private BeneficioService $service) {}

    /**
     * @OA\Get(
     *     path="/api/beneficios",
     *     tags={"Benefícios"},
     *     summary="Lista todos os benefícios ativos",
     *     description="Retorna uma lista paginada de benefícios ativos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de benefícios retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Beneficio")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $beneficios = $this->service->listarAtivos();
        return ApiResponse::success(BeneficioResource::collection($beneficios)->resolve());
    }

    /**
     * @OA\Get(
     *     path="/api/beneficios/{id}",
     *     tags={"Benefícios"},
     *     summary="Exibe um benefício específico",
     *     description="Retorna os dados de um benefício específico pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do benefício",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Benefício retornado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Beneficio")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Benefício não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="sucesso", type="boolean", example=false),
     *             @OA\Property(property="mensagem", type="string", example="Benefício não encontrado")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $beneficio = $this->service->buscarPorId($id);

        if (!$beneficio) {
            return ApiResponse::error('Benefício não encontrado', 404);
        }

        return ApiResponse::success((new BeneficioResource($beneficio))->resolve());
    }
}
