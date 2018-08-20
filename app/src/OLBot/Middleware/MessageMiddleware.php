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

        foreach ($this->storageService->response['main'] as $message) {
            $this->addLine($message, $text);
        }

        foreach ($this->storageService->response['math'] as $message) {
            $this->addLine($message, $text);
        }

        if (!is_null($this->storageService->insult)) {
            $this->addLine('**' . ucwords($this->storageService->insult->insult) . '**', $text);
            if ($this->storageService->insult->author)
                $text .= ' __(' . $this->storageService->insult->author . ')__';
        }

        $this->messageService->sendMessage($text, $this->storageService->message->getChat()->getId());
    }

    private function addLine($message, &$text)
    {
        if ($text) $text .= '\n';
        $text .= $message;
    }
}