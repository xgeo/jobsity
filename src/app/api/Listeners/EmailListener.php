<?php

namespace App\Api\Listeners;

use App\Core\MessageBrokerConnection;

class EmailListener extends MessageBrokerConnection
{
    protected ?string $queue = 'email:stock';
}