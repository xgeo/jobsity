<?php
namespace App\Core;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class MessageBrokerConnection
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;

    protected ?string $queue;

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();
            $this->channel->queue_declare($this->queue);
        } catch (\Exception $e) {
            var_dump($e->getMessage());die;
        }
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function consume($callback)
    {
        $this->channel->basic_consume($this->queue, '', false, true, false, false, $callback);
    }

    public function sendMessage(string $message)
    {
        $this->channel->basic_publish(new AMQPMessage($message), '', $this->queue);
    }
}