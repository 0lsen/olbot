<?php

namespace OLBot\Middleware;


use OLBot\Command\AbstractCommand;
use Slim\Http\Request;
use Slim\Http\Response;

class CommandMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        //TODO: does the API tell me about the usage of a registered command already?

        foreach ($this->storageService->settings->commands as $command) {
            if ($this->commandFound('/' . $command->call)) {
                $commandName = '\OLBot\Command\\'.$command->name;
                /** @var AbstractCommand $commandObject */
                $commandObject = new $commandName($this->storageService, $command->settings);
                $this->storageService->sendResponse = true;

                try {
                    $commandObject->doStuff();
                } catch (\Exception $e) {
                    $this->storageService->response['main'][] = 'ERROR: ' . $e->getMessage();
                }

                return $response;
            }
        }

        return $next($request, $response);
    }

    private function commandFound($call)
    {
        $found = strpos($this->storageService->textCopy, $call) === 0;
        if ($found) {
            $this->storageService->textCopy = preg_replace(
                ['#'.preg_quote($call, '#').'#', '#^\s+#'],
                ['', ''],
                $this->storageService->textCopy,
                1
            );
        }
        return $found;
    }
}