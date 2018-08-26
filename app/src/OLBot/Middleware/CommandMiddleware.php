<?php

namespace OLBot\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class CommandMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        //TODO: does the API tell me about the usage of a registered command already?

        $found = $this->checkCommand('addJoke', 'Joke');
        if ($found) {
            return $response;
        }

        $found = $this->checkCommand('addFlattery', 'Karma', ['karma' => true]);
        if ($found) {
            return $response;
        }

        $found = $this->checkCommand('addInsult', 'Karma', ['karma' => false]);
        if ($found) {
            return $response;
        }

        return $next($request, $response);
    }

    private function checkCommand($command, $eloquentModel, $conditions = [])
    {
        if ($this->commandFound($command)) {
            $this->storageService->sendResponse = true;
            $alreadyKnown = $this->isTextAlreadyKnown($eloquentModel, $this->storageService->textCopy, $conditions);
            $this->storageService->response['main'][] =
                $alreadyKnown
                ? $this->storageService->settings['command']['reply_entry_already_known']
                : $this->storageService->settings['command']['reply_new_entry'];
            if (!$alreadyKnown) {
                $this->addNew($eloquentModel, $this->storageService->textCopy, $this->storageService->user->id, $conditions);
            }

            return true;
        } else {
            return false;
        }
    }

    private function commandFound($command)
    {
        $needle = '/' . $this->storageService->settings['command']['commands'][$command] . ' ';
        $found =
            isset($this->storageService->settings['command']['commands'][$command])
            && strpos($this->storageService->textCopy, $needle) === 0;
        if ($found) {
            $this->storageService->textCopy = str_replace_first(
                $needle,
                '',
                $this->storageService->textCopy
            );
        }
        return $found;
    }

    private function isTextAlreadyKnown($eloquentModel, $text, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::where', array_merge(['text' => $text], $conditions))->count();
    }

    private function addNew($eloquentModel, $text, $author, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::create', array_merge(['text' => $text, 'author' => $author], $conditions));
    }
}