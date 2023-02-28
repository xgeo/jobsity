<?php

namespace App\Core\Contracts;

interface Queueable
{
    public function declareQueue(string $queue): void;
}