<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeneficioController;
use App\Http\Controllers\SolicitacaoBeneficioController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});


Route::prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->middleware(JwtMiddleware::class);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware(JwtMiddleware::class);
    Route::get('perfil', [AuthController::class, 'perfil'])->middleware(JwtMiddleware::class);
});

Route::prefix('beneficios')->group(function () {
    Route::get('/', [BeneficioController::class, 'index'])->middleware(JwtMiddleware::class);
    Route::get('/{beneficio}', [BeneficioController::class, 'show'])->middleware(JwtMiddleware::class);
});

Route::prefix('solicitacoes')->group(function () {
    Route::get('/', [SolicitacaoBeneficioController::class, 'index'])->middleware(JwtMiddleware::class);
    Route::post('/', [SolicitacaoBeneficioController::class, 'store'])->middleware(JwtMiddleware::class);
    Route::get('/{solicitacao}', [SolicitacaoBeneficioController::class, 'show'])->middleware(JwtMiddleware::class);

    Route::get('/pendentes/aprovacao', [SolicitacaoBeneficioController::class, 'pendentesAprovacao'])->middleware(JwtMiddleware::class);
    Route::put('/{solicitacao}/aprovar', [SolicitacaoBeneficioController::class, 'aprovar'])->middleware(JwtMiddleware::class);
    Route::put('/{solicitacao}/rejeitar', [SolicitacaoBeneficioController::class, 'rejeitar'])->middleware(JwtMiddleware::class);
});
