<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Command\AbstractCommand;
use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\MessageEntity;

class CommandMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        AbstractCommand::$storageService = $this->storageService;
        AbstractCategory::$storageService = $this->storageService;

        $entities = $this->storageService->message->getEntities();

        if($entities) {
            foreach ($entities as $entity) {
                if ($entity->getType() == MessageEntity::TYPE_BOT_COMMAND) {
                    $commandCall = substr($this->storageService->textCopy, $entity->getOffset()+1, $entity->getLength()-1);
                    if (isset($this->storageService->settings->commands[$commandCall])) {
                        $command = $this->storageService->settings->commands[$commandCall];
                        $this->storageService->textCopy = preg_replace('#^/'.$commandCall.'\s*#', '', $this->storageService->textCopy, 1);
                        $commandName = '\OLBot\Command\\'.$command->name;
                        /** @var AbstractCommand $commandObject */
                        $commandObject = new $commandName($command->settings);
                        $this->storageService->sendResponse = true;

                        try {
                            $commandObject->doStuff();
                        } catch (\Exception $e) {
                            $this->storageService->response->text[] = 'ERROR: ' . $e->getMessage();
                        }

                        return $response;
                    }
                }
            }
        }

        return $next($request, $response);
    }
}