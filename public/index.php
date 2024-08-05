<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;

require __DIR__ . '/../vendor/autoload.php';

// Load all configuration options
$config = require __DIR__ . '/../config.php';

$app = AppFactory::create();

// Common function to handle Telegram exceptions
function handleTelegramException(TelegramException $e, Response $response): Response {
    $response->getBody()->write($e->getMessage());
    return $response;
}

// Function to create and return a Telegram instance
function createTelegramInstance(array $config): Telegram {
    return new Telegram($config['bot_api_key'], $config['bot_username']);
}

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello world!");
    return $response;
});

// php-telegram-bot
$app->group('/webhook', function(RouteCollectorProxy $group) use ($config) {
    $group->get('/set', function (Request $request, Response $response) use ($config) {
        try {
            $telegram = createTelegramInstance($config);

            $result = $telegram->setWebhook($config['webhook']['url'] . '/webhook/hook');
            $response->getBody()->write($result->getDescription());
        } catch (TelegramException $e) {
            return handleTelegramException($e, $response);
        }

        return $response;
    });

    $group->post('/hook', function (Request $request, Response $response) use ($config) {
        try {
            $telegram = createTelegramInstance($config);

            // Configure bot commands
            $telegram->addCommandsPaths($config['commands']['paths']);

            $telegram->setUpdateFilter(function (Update $update, Telegram $telegram, &$reason = 'Update denied by update_filter') use ($config) {
                $user_id = $update->getMessage()->getFrom()->getId();
                if ($user_id == $config['bot_allowed_id']) {
                    return true;
                }

                $reason = "Invalid user with ID {$user_id}";
                return false;
            });

            $telegram->handle();
            $response->getBody()->write('ok');
        } catch (TelegramException $e) {
            return handleTelegramException($e, $response);
        }

        return $response;
    });

    $group->get('/unset', function (Request $request, Response $response) use ($config) {
        try {
            $telegram = createTelegramInstance($config);

            $result = $telegram->deleteWebhook();
            $response->getBody()->write($result->getDescription());
        } catch (TelegramException $e) {
            return handleTelegramException($e, $response);
        }

        return $response;
    });
});

$app->run();
