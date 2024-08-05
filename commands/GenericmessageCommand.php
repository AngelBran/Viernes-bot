<?php
// src/Commands/GenericmessageCommand.php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);

        // Aquí puedes añadir la lógica que deseas ejecutar cuando se recibe un mensaje de texto
        $response_text = "Recibí tu mensaje: $text";

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => $response_text,
        ]);
    }
}
