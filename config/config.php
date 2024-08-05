<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (getenv('GAE_ENV') !== 'standard') {
    // Carga las variables de entorno desde la raíz
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

return [
    'bot_api_key'       => $_ENV['BOT_API_KEY'],
    'bot_username'      => $_ENV['BOT_USERNAME'],
    'bot_allowed_id'    => $_ENV['BOT_ALLOWED_ID'],

    'webhook'           => [
        'url'       => $_ENV['APP_URL'],
    ],

    'mysql'        => [
        'host'     => $_ENV['MYSQL_HOST'],
        'user'     => $_ENV['MYSQL_USER'],
        'password' => $_ENV['MYSQL_PASSWORD'],
        'database' => $_ENV['MYSQL_DATABASE'],
    ],

    'commands'          => [
        'paths'     => [
            __DIR__ . '/commands',
        ],
    ],

    'chatgpt'           => [
        'endpoint'  => [
            'url'       => 'https://api.openai.com/v1/assistants',
            'headers'   => [
                'Authorization' => 'Bearer ' . $_ENV['CHATGPT_API_KEY'],
                'OpenAI-Beta'   => 'assistants=v2',
            ],
        ],
        'model'         => $_ENV['CHATGPT_MODEL'],
    ],
];
