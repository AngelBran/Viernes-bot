<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (getenv('GAE_ENV') !== 'standard') {
    // Carga las variables de entorno desde la raíz
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Load all configuration options
$config = require __DIR__ . '/config.php';

return [
    'migration_dirs' => [
        'migrations' => dirname(__DIR__) . '/migrations',
    ],
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => $_ENV['MYSQL_HOST'],
            'port' => 3306, // optional
            'username' => $_ENV['MYSQL_USER'],
            'password' => $_ENV['MYSQL_PASSWORD'],
            'db_name' => $_ENV['MYSQL_DATABASE'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_spanish_ci', // optional, if not set default collation for utf8mb4 is used
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['MYSQL_HOST'],
            'port' => 3306, // optional
            'username' => $_ENV['MYSQL_USER'],
            'password' => $_ENV['MYSQL_PASSWORD'],
            'db_name' => $_ENV['MYSQL_DATABASE'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_spanish_ci', // optional, if not set default collation for utf8mb4 is used
        ],
    ],
    'default_environment' => $_ENV['APP_ENV'],
    'log_table_name' => 'phoenix_log',
];