<?php

namespace App\Api\Contracts;

interface LoadCSVStringContract
{
    public function loadCSVString(string $string, string $head): void;
}