<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\Update;

require __DIR__ . '/../vendor/autoload.php';

// Load all configuration options
/** @var array $config */
$config = require __DIR__ . '/../config.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");

    return $response;
});

# php-telegram-bot
$app->group('/webhook', function(RouteCollectorProxy $group) use ($config) {
    $group->get('/set', function (Request $request, Response $response, $args) use ($config) {
        try {
            // Create Telegram API object
            $telegram = new Telegram($config['bot_api_key'], $config['bot_username']);

            // Set webhook
            $result = $telegram->setWebhook($config['webhook']['url'] . '/webhook/hook');
            if ($result->isOk()) {
                $response->getBody()->write($result->getDescription());

                return $response;
            }
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            $response->getBody()->write($e->getMessage());

            return $response;
        }
    });

    $group->post('/hook', function (Request $request, Response $response, $args) use ($config) {
        try {
            // Create Telegram API object
            $telegram = new Telegram($config['bot_api_key'], $config['bot_username']);

            // Add commands paths containing your custom commands
            $telegram->addCommandsPaths($config['commands']['paths']);

            $telegram->setUpdateFilter(function (Update $update, Telegram $telegram, &$reason = 'Update denied by update_filter') use ($config) {
                $user_id = $update->getMessage()->getFrom()->getId();
                if ($user_id === $config['bot_allowed_id']) {
                    return true;
                }

                $reason = "Invalid user with ID {$user_id}";
                return false;
            });

            // Handle telegram webhook request
            $telegram->handle();

            $response->getBody()->write('ok');
            return $response;
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            $response->getBody()->write($e->getMessage());

            return $response;
        }
    });

});

$app->run();
