<?php

namespace App\Http\Controllers;

use App\Models\Beneficio;
use App\Http\Resources\BeneficioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BeneficioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $beneficios = Beneficio::ativos()
            ->orderBy('nome')
            ->get();

        return BeneficioResource::collection($beneficios);
    }

    public function show(Beneficio $beneficio): BeneficioResource
    {
        return new BeneficioResource($beneficio);
    }
}