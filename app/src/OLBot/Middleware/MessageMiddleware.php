<?php

namespace OLBot\Middleware;


use OLBot\Service\MessageService;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Client\ObjectSerializer;

class MessageMiddleware
{
    /** @var StorageService */
    private $storageService;
    /** @var MessageService */
    private $messageService;

    public function __construct($storageService, $messageService)
    {
        $this->storageService = $storageService;
        $this->messageService = $messageService;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $messageObject = json_decode(json_encode($request->getParsedBodyParam('message')));
        $this->storageService->message = ObjectSerializer::deserialize($messageObject, 'Swagger\Client\Telegram\Message');

        $response = $next($request, $response);

        $this->sendResponse();

        return $response;
    }

    private function sendResponse()
    {
        if (!$this->storageService->sendResponse) {
            return;
        }

        $text = '';

        foreach ($this->storageService->main as $message) {
            $text .= '\n' . $message;
        }

        foreach ($this->storageService->math as $message) {
            $text .= '\n' . $message;
        }

        $this->messageService->sendMessage($text, $this->storageService->message->getChat()->getId());
    }
}