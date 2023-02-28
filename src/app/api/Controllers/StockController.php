<?php
namespace App\Api\Controllers;
use App\Api\Events\EmailEvent;
use App\Api\Services\CSVService;
use App\Api\Services\StockService;
use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @OA\Tag(name="Stocks")
 */
class StockController extends BaseController
{
    /**
     * @param ContainerInterface $container
     * @param StockService $stockService
     * @param CSVService $CSVService
     * @param EmailEvent $emailEvent
     */
    public function __construct(public ContainerInterface $container,
                                private readonly StockService $stockService,
                                private readonly CSVService $CSVService,
                                private readonly EmailEvent $emailEvent)
    {
        parent::__construct($container);
    }

    /**
     * @OA\Get(
     *     tags={"Stocks"},
     *     path="/stock",
     *     description="",     *
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="q",
     *          parameter="q",
     *          label="Stock Code",
     *          description="Stock code",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     operationId="getStock",
     *     @OA\Response(response="200",
     *     description="An example resource")
     * )
     */
    public function getStock(ServerRequestInterface $request,
                              ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $response = $this->stockService->fetchCSV($params['q']);

        if (!$response) {
            return new JsonResponse(['message' => 'NOT_FOUND'], 404);
        }

        $this->CSVService->loadCSVString($response, 'Symbol,Date,Time,Open,High,Low,Close,Volume,Name');

        $responseArray = $this->CSVService->toArray();

        if (!$this->stockService->isValid($responseArray)) {
            return new JsonResponse(['message' => 'NOT_VALID'], 422);
        }

        $stock = $this->stockService->store(
            $this->stockService->getNewStockModel($responseArray)
        );

        if (!$stock) {
            return new JsonResponse(['message' => 'INSERT_FAIL'], 500);
        }

        ['email' => $email] = $request->getAttribute('token');

        $fiber = new \Fiber(function () use ($email, $responseArray): void {
            $json = json_encode($responseArray);
            $this->emailEvent->sendMessage("{$email}|{$json}");
            \Fiber::suspend('fiber');
        });

        $fiber->start();

        return new JsonResponse([
            'name'      => $stock->getName(),
            'symbol'    => $stock->getSymbol(),
            'open'      => $stock->getOpen(),
            'high'      => $stock->getHigh(),
            'low'       => $stock->getLow(),
            'close'     => $stock->getClose(),
        ], 200);
    }
}