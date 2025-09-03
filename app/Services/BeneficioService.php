<?php
namespace App\Services;

use App\Models\Beneficio;

class BeneficioService
{
    public function listarAtivos()
    {
        return Beneficio::ativos()->orderBy('nome')->get();
    }

    public function buscarPorId(int $id): ?Beneficio
    {
        return Beneficio::find($id);
    }
}
