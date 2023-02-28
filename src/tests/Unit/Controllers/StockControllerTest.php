<?php

namespace Unit\Controllers;

use App\Api\Controllers\StockController;
use App\Api\Events\EmailEvent;
use App\Api\Services\CSVService;
use App\Api\Services\StockService;
use App\Models\Stock;
use DI\Container;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StockControllerTest extends TestCase
{
    private $stockController;
    private $stockService;

    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $container = new Container();
        $stockService = $this->createMock(StockService::class);

        $this->stock = new \App\Models\Stock();
        $this->stock->setId(1);
        $this->stock->setName('KGHM');
        $this->stock->setSymbol('KGH');
        $this->stock->setClose(124);
        $this->stock->setLow(123.65);
        $this->stock->setHigh(127.85);
        $this->stock->setOpen(127.85);
        $this->stock->setDate('2023-02-27', '10:00:00');
        $stockService->method('getNewStockModel')->willReturn($this->stock);

        $csvService = $this->createMock(CSVService::class);
        $csvService->method('toArray')->willReturn([
            'name' => 'KGHM',
            'symbol' => 'KGH',
            'close' => 124,
            'low' => 123.65,
            'high' => 127.85,
            'open' => 127.85,
            'date' => '2023-02-27',
            'time' => '12:00:00'
        ]);

        $container->set(CSVService::class, $csvService);
        $container->set(StockService::class, $stockService);
        $container->set(EmailEvent::class, $this->createMock(EmailEvent::class));

        $this->stockService = $stockService;
        $this->stockController = new StockController($container,
            $container->get(StockService::class),
            $container->get(CSVService::class),
            $container->get(EmailEvent::class)
            );
    }

    public function testGetStock()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn(['q' => 'dy.f']);

        $response = $this->createMock(ResponseInterface::class);

        $this->stockService->method('fetchCSV')
            ->willReturn('Symbol,Date,Time,Open,High,Low,Close,Volume,Name
        KGH,2023-02-24,17:04:14,127.85,127.85,123.65,124,739845,KGHM');

        $this->stockService->method('isValid')->willReturn(true);
        $this->stockService->method('store')->willReturn($this->stock);

        $assert = $this->stockController->getStock($request, $response);

        $this->assertSame(200, $assert->getStatusCode());
    }

    public function testGetStockNotFound()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn(['q' => 'stuff']);

        $response = $this->createMock(ResponseInterface::class);

        $this->stockService->method('fetchCSV')->willReturn(null);

        $response = $this->stockController->getStock($request, $response);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testGetStockInvalidData()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn(['q' => 'stuff']);

        $response = $this->createMock(ResponseInterface::class);

        $this->stockService->method('fetchCSV')
            ->willReturn('Symbol,Date,Time,Open,High,Low,Close,Volume,Name
        KGH,N/D,N/D,N/D,N/D,N/D,N/D,N/D,N/D');

        $this->stockService->method('isValid')->willReturn(false);

        $response = $this->stockController->getStock($request, $response);

        $this->assertSame(422, $response->getStatusCode());
    }
}