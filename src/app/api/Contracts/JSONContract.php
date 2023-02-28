<?php

namespace App\Api\Contracts;

interface JSONContract
{
    public function toJSON(): string;
}