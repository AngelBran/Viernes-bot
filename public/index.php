<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

// Load all configuration options
/** @var array $config */
$config = require __DIR__ . '/../config.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");

    return $response;
});

$app->get('/test_config', function (Request $request, Response $response, $args) use ($config) {
    $response->getBody()->write(json_encode($config));

    return $response;
});

# php-telegram-bot
$app->group('/webhook', function(RouteCollectorProxy $group) use ($config) {
    $group->get('/set', function (Request $request, Response $response, $args) use ($config) {
        try {
            // Create Telegram API object
            $telegram = new Longman\TelegramBot\Telegram($config['bot_api_key'], $config['bot_username']);

            // Set webhook
            $result = $telegram->setWebhook($config['webhook']['url']);
            if ($result->isOk()) {
                $response->getBody()->write($result->getDescription());

                return $response;
            }
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            $response->getBody()->write($e->getMessage());

            // log telegram errors
            return $response;
        }
    });

    $group->get('/hook', function (Request $request, Response $response, $args) use ($config) {
        try {
            // Create Telegram API object
            $telegram = new Longman\TelegramBot\Telegram($config['bot_api_key'], $config['bot_username']);

            // Handle telegram webhook request
            $telegram->handle();
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            // Silence is golden!
            // log telegram errors
            echo $e->getMessage();
        }
    });

});

$app->run();
