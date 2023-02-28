<?php

namespace App\Api\Controllers;

use App\Api\Services\StockService;
use App\Models\Stock;
use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HistoryController extends BaseController
{
    /**
     * @param ContainerInterface $container
     * @param StockService $stockService
     */
    public function __construct(ContainerInterface $container,
                                private readonly StockService $stockService)
    {
        parent::__construct($container);
    }

    /**
     * @OA\Get(
     *     tags={"Stocks"},
     *     path="/history",
     *     description="",
     *     operationId="getHistories",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200",
     *     description="An example resource")
     * )
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getHistories(ServerRequestInterface $request,
                                 ResponseInterface $response): ResponseInterface
    {
        $data = array_map(function (Stock $stock) {;
            return [
                'date'      => $stock->getDate(),
                'open'      => $stock->getOpen(),
                'high'      => $stock->getHigh(),
                'low'       => $stock->getLow(),
                'close'     => $stock->getClose(),
                'name'      => $stock->getName()
            ];
        }, $this->stockService->histories());

        return new JsonResponse($data, 200);
    }
}