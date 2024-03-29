<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Logger;
use OLBot\Model\DB\Answer;
use OLBot\Service\MessageService;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\Message;
use Telegram\ObjectSerializer;

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
        $message = ObjectSerializer::deserialize($messageObject, 'Telegram\Model\Message');

        if (!$message || Logger::messageInAlreadyLogged($message)) return $response;

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
                Logger::logError($this->storageService->message->getMessageId(), $t);
                $answers = Answer::where(['category' => AbstractCategory::CAT_ERROR]);
                if (!$answers->count()) throw new \Exception('no error answer found.');
                $answer = $answers->inRandomOrder()->first()->text;
            } catch (\Throwable $t2) {
                $answer = $this->storageService->settings->getFallbackErrorResponse();
                Logger::logError($this->storageService->message->getMessageId(), $t2);
            }
            $this->storageService->sendResponse = true;
            $this->storageService->response->text = [$answer];
        }

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

        if (sizeof($this->storageService->response->text) || !is_null($this->storageService->karma)) {
            $this->sendMessage();
        }

        if (sizeof($this->storageService->response->pics)) {
            $this->sendPictures();
        }
    }

    private function sendMessage()
    {
        $text = '';

        foreach ($this->storageService->response->text as $message) {
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

    private function sendPictures()
    {
        foreach ($this->storageService->response->pics as $pic) {
            $this->messageService->sendPicture(
                $pic,
                $this->storageService->message->getChat()->getId(),
                $this->storageService->message->getMessageId()
            );
        }
    }

    private function addLine($message, &$text)
    {
        if ($text) $text .= "\n";
        $text .= $message;
    }
}