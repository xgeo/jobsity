<?php
namespace App\Api\Services;

use App\Models\Stock;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service responsible to fetch any data from stock api
 */
class StockService
{
    public const URI = 'https://stooq.com/q/l/';

    /**
     * @param EntityManager $entityManager
     * @param Client $client
     */
    public function __construct(private readonly EntityManager $entityManager,
                                private readonly Client $client) {}

    /**
     * @return array
     */
    public function histories(): array
    {
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        return $stockRepository->findAll();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        return !in_array('N/D', array_values($data));
    }

    /**
     * @param array $data
     * @return Stock|null
     */
    public function getNewStockModel(array $data): ?Stock
    {
        $stock = new Stock();

        $stock->setName($data['name']);
        $stock->setSymbol($data['symbol']);
        $stock->setClose(floatval($data['close']));
        $stock->setOpen(floatval($data['open']));
        $stock->setLow(floatval($data['low']));
        $stock->setHigh(floatval($data['high']));
        $stock->setDate($data['date'], $data['time']);

        return $stock;
    }

    /**
     * @param Stock $stock
     * @return Stock|null
     */
    public function store(Stock $stock): ?Stock
    {
        try {
            $this->entityManager->persist($stock);
            $this->entityManager->flush();

            return $stock;
        } catch (Exception|ORMException $e) {
            return null;
        }
    }

    /**
     * @param string $stockCode
     * @return string|null
     */
    public function fetchCSV(string $stockCode): ?string
    {
        try {
            $response = $this->client->get("?s={$stockCode}&f=sd2t2ohlcvn&h&e=csv");

            if ($response->getStatusCode() != 200) {
                return null;
            }

            return $response->getBody()->getContents();
        } catch (GuzzleException $guzzleException) {
            return null;
        }
    }
}