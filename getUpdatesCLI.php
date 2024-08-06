#!/usr/bin/env php
<?php

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\TelegramLog;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

// Load all configuration options
/** @var array $config */
$config = require __DIR__ . '/config/config.php';

try {
    // Create Telegram API object
    $telegram = new Telegram($config['bot_api_key'], $config['bot_username']);

    // Enable MySQL
    $telegram->enableMySql($config['mysql']);

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

    TelegramLog::initialize(
        // Main logger that handles all 'debug' and 'error' logs.
        new Logger('telegram_bot', [
            (new StreamHandler( __DIR__ . '/logs/debug_log_file.txt', Logger::DEBUG))->setFormatter(new LineFormatter(null, null, true)),
            (new StreamHandler( __DIR__ . '/logs/error_log_file.txt', Logger::ERROR))->setFormatter(new LineFormatter(null, null, true)),
        ]),
        // Updates logger for raw updates.
        new Logger('telegram_bot_updates', [
            (new StreamHandler( __DIR__ . '/logs/updates_log_file.txt', Logger::INFO))->setFormatter(new LineFormatter('%message%' . PHP_EOL)),
        ])
    );

    // Handle telegram getUpdates request
    $server_response = $telegram->handleGetUpdates();

    if ($server_response->isOk()) {
        $update_count = count($server_response->getResult());
        echo date('Y-m-d H:i:s') . ' - Processed ' . $update_count . ' updates';
    } else {
        echo date('Y-m-d H:i:s') . ' - Failed to fetch updates' . PHP_EOL;
        echo $server_response->printError();
    }

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);

    // Uncomment this to output any errors (ONLY FOR DEVELOPMENT!)
    echo $e;
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Uncomment this to output log initialisation errors (ONLY FOR DEVELOPMENT!)
    echo $e;
}
