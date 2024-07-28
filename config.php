<?php

use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (getenv('GAE_ENV') !== 'standard') {
    // Carga las variables de entorno
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

return [
    'bot_api_key'  => $_ENV['BOT_API_KEY'],
    'bot_username' => $_ENV['BOT_USERNAME'],

    'webhook' => [
        'url' => $_ENV['APP_URL'],
    ]
];
