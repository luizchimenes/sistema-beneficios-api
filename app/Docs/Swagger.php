<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Sistema Benefícios",
 *     version="1.0.0",
 *     description="Documentação da API do Sistema de Benefícios",
 *     @OA\Contact(
 *         email="gustavo.chimenesp@gmail.com",
 *         name="Luiz Chimenes"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor local"
 * )
 */
class Swagger
{
    // Este arquivo só serve para armazenar as anotações globais
}
