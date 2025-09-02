<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeneficioController;
use App\Http\Controllers\SolicitacaoBeneficioController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['jwt.verify'])->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('perfil', [AuthController::class, 'perfil']);
    });

    Route::prefix('beneficios')->group(function () {
        Route::get('/', [BeneficioController::class, 'index']);
        Route::get('/{beneficio}', [BeneficioController::class, 'show']);
    });

    Route::prefix('solicitacoes')->group(function () {
        Route::get('/', [SolicitacaoBeneficioController::class, 'index']);
        Route::post('/', [SolicitacaoBeneficioController::class, 'store']);
        Route::get('/{solicitacao}', [SolicitacaoBeneficioController::class, 'show']);
        
        // Rotas para aprovadores
        Route::get('/pendentes/aprovacao', [SolicitacaoBeneficioController::class, 'pendentesAprovacao']);
        Route::put('/{solicitacao}/aprovar', [SolicitacaoBeneficioController::class, 'aprovar']);
        Route::put('/{solicitacao}/rejeitar', [SolicitacaoBeneficioController::class, 'rejeitar']);
    });
});