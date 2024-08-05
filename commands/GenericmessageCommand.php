<?php
// src/Commands/GenericmessageCommand.php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        // Load all configuration options
        $config = require __DIR__ . '/config/config.php';

        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);

        // Aquí puedes añadir la lógica que deseas ejecutar cuando se recibe un mensaje de texto
        /* $response_text = "Recibí tu mensaje: $text";

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => $response_text,
        ]); */

        $client = new Client();

        try {
            // Realizar la solicitud GET a la API con los headers especificados
            $api_response = $client->request('GET', $config['chatgpt']['endpoint']['url'], [
                'headers' => $config['chatgpt']['endpoint']['headers'],
            ]);

            // Obtener el cuerpo de la respuesta de la API
            $body = $api_response->getBody();

            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text'    => $body,
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Manejar el error de la solicitud
            $error_message = [
                'error' => 'Error al consultar la API',
                'message' => $e->getMessage(),
            ];

            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text'    => json_encode($error_message),
            ]);
        }
    }
}
