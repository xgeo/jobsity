<?php

namespace App\Api\Controllers;

use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;

/**
 * @OA\OpenApi(
 *   security={{"bearerAuth": {}}}
 * )
 *
 * @OA\Info(description="Jobsity",
 *     title="Jobsity API",
 *     version="0.1"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      name="Authorization",
 *      in="header"
 * )
 */
class BaseController
{
    public function __construct(public ContainerInterface $container)
    {
    }
}