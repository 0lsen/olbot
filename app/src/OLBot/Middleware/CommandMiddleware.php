<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Command\AbstractCommand;
use OLBot\Command\StorageCommand;
use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\MessageEntity;

class CommandMiddleware extends TextBasedMiddleware
{
    /** @var StorageCommand[] */
    private $commands = [];

    public function __invoke(Request $request, Response $response, $next)
    {
        AbstractCommand::setStandardReplyToNewEntry($this->storageService->settings->getCommand()->getReplyToNewEntry());
        AbstractCommand::setStandardReplyToEntryAlreadyKnown($this->storageService->settings->getCommand()->getReplyToEntryAlreadyKnown());
        AbstractCommand::setStandardReplyToInvalidInput($this->storageService->settings->getCommand()->getReplyToInvalidInput());
        StorageCommand::setStorageService($this->storageService);

        AbstractCategory::setStorageService($this->storageService);
        AbstractCategory::setCacheService($this->cacheService);

        $entities = $this->storageService->message->getEntities();

        if($entities) {
            $this->buildCommands();
            foreach ($entities as $entity) {
                if ($entity->getType() == MessageEntity::TYPE_BOT_COMMAND) {
                    $command = substr($this->storageService->textCopy, $entity->getOffset()+1, $entity->getLength()-1);
                    $commandClean = preg_replace("#@".$this->storageService->settings->getBotName()."#", "", $command, 1);
                    if (isset($this->commands[$commandClean])) {
                        $this->storageService->textCopy = preg_replace('#^/'.$command.'\s*#', '', $this->storageService->textCopy, 1);
                        $this->storageService->sendResponse = true;

                        try {
                            $this->commands[$commandClean]->doStuff();
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

    private function buildCommands()
    {
        foreach ($this->storageService->settings->getCommand()->getCommandList() as $command) {
            $commandName = '\OLBot\Command\\'.$command->getCommandType();
            $this->commands[$command->getName()] = new $commandName($command);
        }
    }
}