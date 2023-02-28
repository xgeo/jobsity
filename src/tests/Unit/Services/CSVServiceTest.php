<?php
declare(strict_types=1);

use App\Api\Services\CSVService;
use PHPUnit\Framework\TestCase;

class CSVServiceTest extends TestCase
{
    private CSVService $CSVService;
    private string $csv = 'Symbol,Date,Time,Open,High,Low,Close,Volume,Name
KGH,2023-02-24,17:04:14,127.85,127.85,123.65,124,739845,KGHM';

    protected function setUp(): void
    {
        parent::setUp();
        $this->CSVService = new CSVService();
        $this->CSVService->loadCSVString($this->csv, 'Symbol,Date,Time,Open,High,Low,Close,Volume,Name');
    }

    public function testJSON(): void
    {
        $json = $this->CSVService->toJSON();

        $this->assertNotEmpty($json);
    }

    public function testArray(): void
    {
        $data = $this->CSVService->toArray();

        $this->assertIsArray($data);
    }

}