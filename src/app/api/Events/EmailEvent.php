<?php

namespace App\Api\Events;

use App\Core\MessageBrokerConnection;

class EmailEvent extends MessageBrokerConnection
{
    protected ?string $queue = 'email:stock';
}