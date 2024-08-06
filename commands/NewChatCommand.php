<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use GuzzleHttp\Client;
use Longman\TelegramBot\Request;

class NewChatCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'new_chat';

    /**
     * @var string
     */
    protected $description = 'Create new chat in ChatGPT';

    /**
     * @var string
     */
    protected $usage = '/new_chat';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        // If you use deep-linking, get the parameter like this:
        // $deep_linking_parameter = $this->getMessage()->getText(true);

        /* return $this->replyToChat(
            'Hi there!' . PHP_EOL .
            'Type /help to see all commands!'
        ); */

        // Load all configuration options
        $config = require __DIR__ . '/config/config.php';

        $message = $this->getMessage();
        $chat_id    = $message->getChat()->getId();

        $client = new Client();

        try {
            // Realizar la solicitud GET a la API con los headers especificados
            $response = $client->request('POST', $config['chatgpt']['endpoint']['url'] . '/threads', [
                'headers' => $config['chatgpt']['endpoint']['headers'],
            ]);

            if ($response->getStatusCode() != 200) {
                throw new \Exception('Error al realizar la solicitud POST');
            }

            // Obtener el cuerpo de la respuesta de la API
            $body = $response->getBody();

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