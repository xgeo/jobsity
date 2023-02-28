<?php

use App\Api\Listeners\EmailListener as MaiListener;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = new MaiListener();

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    try {
        $data = explode('|', $msg->body);

        (new \App\Api\Jobs\EmailSenderJob())
            ->handle($data[0], $data[1]);
    } catch (Exception $e) {
        var_dump($e);
    }
};

$connection->consume($callback);


while(count($connection->getChannel()->callbacks)) {
    $connection->getChannel()->wait();
}

$connection->getChannel()->close();
$connection->getConnection()->close();