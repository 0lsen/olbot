<?php

namespace OLBot\Middleware;


use OLBot\Logger;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Client\ObjectSerializer;
use Swagger\Client\Telegram\Message;

class MessageMockMiddleware extends TextBasedMiddleware
{
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

        return $next($request, $response);
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
}