<?php

namespace OLBot\Middleware;


use OLBot\Command\AbstractCommand;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Client\Telegram\MessageEntity;

class CommandMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $entities = $this->storageService->message->getEntities();

        if($entities) {
            foreach ($entities as $entity) {
                if ($entity->getType() == MessageEntity::TYPE_BOT_COMMAND) {
                    $commandCall = substr($this->storageService->textCopy, $entity->getOffset()+1, $entity->getLength()-1);
                    if (isset($this->storageService->settings->commands[$commandCall])) {
                        $command = $this->storageService->settings->commands[$commandCall];
                        $this->storageService->textCopy = str_replace_first('/'.$commandCall.' ', '', $this->storageService->textCopy);
                        $commandName = '\OLBot\Command\\'.$command->name;
                        /** @var AbstractCommand $commandObject */
                        $commandObject = new $commandName($this->storageService, $command->settings);
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