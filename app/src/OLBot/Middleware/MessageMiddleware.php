<?php

namespace OLBot\Middleware;


use OLBot\Logger;
use OLBot\Service\MessageService;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Client\ObjectSerializer;
use Swagger\Client\Telegram\Message;

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
        /** @var Message $message */
        $message = ObjectSerializer::deserialize($messageObject, 'Swagger\Client\Telegram\Message');

        Logger::logMessageIn($message);

        if ($this->insufficientMessageData($message)) {
            return $response;
        }

        $this->storageService->message = $message;
        $this->storageService->textCopy = $message->getText();

        $response = $next($request, $response);

        $this->sendResponse();

        return $response;
    }

    private function insufficientMessageData(Message $message)
    {
        return
            !$message->getText()
            || !$message->getChat()
            || !$message->getChat()->getId()
            || !$message->getFrom()
            || !$message->getFrom()->getId();
    }

    private function sendResponse()
    {
        if (!$this->storageService->sendResponse) {
            return;
        }

        $text = '';

        foreach ($this->storageService->response->main as $message) {
            $this->addLine($message, $text);
        }

        if (!is_null($this->storageService->karma)) {
            $this->addLine(ucwords($this->storageService->karma->text), $text);
        }

        $this->messageService->sendMessage(
            $text,
            $this->storageService->message->getChat()->getId(),
            $this->storageService->message->getMessageId()
        );
    }

    private function addLine($message, &$text)
    {
        if ($text) $text .= '\n';
        $text .= $message;
    }
}