<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Logger;
use OLBot\Model\DB\Answer;
use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\Message;
use Telegram\ObjectSerializer;

class MessageMockMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $messageObject = json_decode(json_encode($request->getParsedBodyParam('message')));
        /** @var Message $message */
        $message = ObjectSerializer::deserialize($messageObject, 'Telegram\Model\Message');

        Logger::logMessageIn($message);

        if ($this->insufficientMessageData($message)) {
            return $response;
        }

        $this->storageService->message = $message;
        $this->storageService->textCopy = $message->getText();

        try {
            $response = $next($request, $response);
        } catch (\Throwable $t) {
            try {
                $answers = Answer::where(['category' => AbstractCategory::CAT_ERROR]);
                if (!$answers->count()) throw new \Exception('no error answer found.');
                $answer = $answers->inRandomOrder()->first()->text;
            } catch (\Throwable $t2) {
                $answer = $this->storageService->settings->fallbackErrorResponse;
            }
            $this->storageService->sendResponse = true;
            $this->storageService->response->text = [$answer];
        }

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
}