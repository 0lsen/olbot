<?php

namespace OLBot\Middleware;


use Illuminate\Database\Eloquent\Collection;
use OLBot\Model\DB\Insult;
use Slim\Http\Request;
use Slim\Http\Response;

class InsultMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $text = $this->storageService->message->getText();
        $insulted = false;
        $insults = $this->getInsults();
        foreach ($insults as $insult) {
            if (strpos($text, $insult->insult) !== false) {
                $insulted = true;
                $this->removeFromText($insult->insult);
            }
        }

        if ($insulted) {
            $this->storageService->insult = $insults->random();
            $this->storageService->sendResponse = true;
        }

        return $next($request, $response);
    }

    private function getInsults() : Collection
    {
//        if (apcu_exists('olbot_insults')) {
//            return apcu_fetch('olbot_insults');
//        } else {
            $insults = Insult::all();
//            apcu_add('olbot_insults', $insults);
            return $insults;
//        }
    }
}