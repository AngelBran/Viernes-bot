<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Carga las variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

# php-telegram-bot
$app->group('/webhook', function(RouteCollectorProxy $group) {
    $group->get('/set', function (Request $request, Response $response, $args) {
        $bot_api_key  = getenv('BOT_API_KEY');
        $bot_username = getenv('BOT_USERNAME');
        $hook_url     = getenv('APP_URL') . '/webhook/hook';

        try {
            // Create Telegram API object
            $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

            // Set webhook
            $result = $telegram->setWebhook($hook_url);
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            // log telegram errors
            echo $e->getMessage();
        }
    });
});

$app->run();
