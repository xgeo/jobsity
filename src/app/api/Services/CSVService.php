<?php

namespace App\Api\Services;

use App\Api\Contracts\ArrayContract;
use App\Api\Contracts\JSONContract;
use App\Api\Contracts\LoadCSVStringContract;

class CSVService implements LoadCSVStringContract, JSONContract, ArrayContract
{
    private ?array $values;
    private array $head = [];

    /**
     * @param string $string
     * @param string $head
     * @return void
     */
    public function loadCSVString(string $string, string $head): void
    {
        $this->values   = explode(',', trim(str_replace($head, '', $string)));
        $this->head     = explode(',', $head);
    }

    /**
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this->values);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = array_combine($this->head, $this->values);
        return array_change_key_case($array, CASE_LOWER);
    }
}