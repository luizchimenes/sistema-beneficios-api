<?php

namespace App\Http\Controllers;

use App\Models\Beneficio;
use App\Http\Resources\BeneficioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BeneficioController extends Controller
{

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
    public function index(): AnonymousResourceCollection
    {
        $beneficios = Beneficio::ativos()
            ->orderBy('nome')
            ->get();

        return BeneficioResource::collection($beneficios);
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
    public function show(Beneficio $beneficio): BeneficioResource
    {
        return new BeneficioResource($beneficio);
    }
}
