<?php


namespace OLBot\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\MessageEntity;

class AdressedMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        return $this->shouldICare()
            ? $next($request, $response)
            : $response;
    }

    private function shouldICare()
    {
        return $this->storageService->message->getChat()->getId() > 0 || $this->wasIMentioned();
    }

    private function wasIMentioned()
    {
        $entities = $this->storageService->message->getEntities();
        if ($entities) {
            foreach ($entities as $entity) {
                if (
                    $entity->getType() == MessageEntity::TYPE_MENTION
                    && $entity->getOffset() === 0
                    && substr($this->storageService->textCopy, 1, $entity->getLength()-1) == $this->storageService->settings->botName
                ) {
                    $text = $this->storageService->textCopy;
                    $text = str_replace_first('@' . $this->storageService->settings->botName . ' ', '', $text);
                    $this->storageService->textCopy = $text;

                    return true;
                }
            }
        }

        return false;
    }
}