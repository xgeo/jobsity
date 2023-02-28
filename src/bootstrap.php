<?php

use App\Api\Controllers\HistoryController;
use App\Api\Controllers\UserController;
use App\Api\Services\UserService;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use GuzzleHttp\Client;
use App\Api\Services\StockService;
use App\Api\Services\CSVService;
use App\Api\Controllers\StockController;

require_once __DIR__ . '/vendor/autoload.php';

const SRC_PATH = __DIR__;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container(require __DIR__ . '/app/config/settings.php');

$container->set(EntityManager::class, static function (Container $c): EntityManager {
    /** @var array $settings */
    $settings = $c->get('settings');

    $config = ORMSetup::createAttributeMetadataConfiguration(
        $settings['doctrine']['metadata_dirs'],
        $settings['doctrine']['dev_mode'],
        null
    );

    return new EntityManager(
        new Connection($settings['doctrine']['connection'],
            new Driver()), $config);
});

$container->set(StockService::class, static function(Container $c) {
    return new StockService($c->get(EntityManager::class), new Client([
        'base_uri' => StockService::URI
    ]));
});

$container->set(UserService::class, static function(Container $c) {
    return new UserService($c->get(EntityManager::class));
});

$container->set(CSVService::class, static function() {
    return new CSVService();
});

$app = Bridge::create($container);

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => $_ENV['JWT_SECRET'],
    "ignore" => ["/user/auth"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];

        $response->getBody()->write(
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        return $response->withHeader("Content-Type", "application/json");
    }
]));

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->get('/stock[/{params:.*}]', StockController::class . ':getStock');

$app->get('/history', HistoryController::class . ':getHistories');

$app->post('/user/auth', UserController::class . ':auth');

$app->post('/user', UserController::class . ':store');

/**
 * Generate swagger docs
 */
$openapi = \OpenApi\Generator::scan([SRC_PATH . '/app/api']);
file_put_contents(SRC_PATH . '/public/api.json', $openapi->toJson(), LOCK_EX);

$app->run();