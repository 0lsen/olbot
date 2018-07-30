<?php

namespace OLBot\Middleware;


use OLBot\Service\MessageService;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;

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
        //TODO: deserialise message to swagger model
        $this->storageService->message = $request->getParsedBody();
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

        $this->messageService->sendMessage($text, $this->storageService->message['message']['chat']['id']);
    }
}