<?php

namespace OLBot\Middleware;


use Illuminate\Database\Eloquent\Collection;
use OLBot\Model\DB\Karma;
use Slim\Http\Request;
use Slim\Http\Response;

class KarmaMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $text = $this->storageService->message->getText();
        $karma = null;
        $insults = $this->getInsults();
        foreach ($insults as $insult) {
            if (strpos($text, $insult->text) !== false) {
                $karma = false;
                $this->removeFromText($insult->text);
            }
        }
        if (is_null($karma)) {
            $flattery = $this->getFlattery();
            foreach ($flattery as $flatter) {
                if (strpos($text, $flatter->text) !== false) {
                    $karma = true;
                    $this->removeFromText($flatter->text);
                }
            }
        }

        if (!is_null($karma)) {
            $this->storageService->sendResponse = true;
            $this->manipulateKarma($karma);
        }

        if (
            is_null($karma)
            && $this->storageService->user
            && $this->storageService->user->karma
            && rand(0,100)/100 < abs($this->storageService->user->karma)
        ) {
            $karma = $this->storageService->user->karma > 0;
        }

        if (!is_null($karma)) {
            $this->storageService->karma = $karma ? $flattery->random() : $insults->random();
        }

        return $next($request, $response);
    }

    private function getInsults() : Collection
    {
//        if (apcu_exists('olbot_insults')) {
//            return apcu_fetch('olbot_insults');
//        } else {
            $insults = Karma::where(['karma' => false])->get();
//            apcu_add('olbot_insults', $insults);
            return $insults;
//        }
    }

    //TODO: function above and below should probably be one

    private function getFlattery() : Collection
    {
//        if (apcu_exists('olbot_flattery')) {
//            return apcu_fetch('olbot_flattery');
//        } else {
        $flattery = Karma::where(['karma' => true])->get();
//            apcu_add('olbot_flattery', $flattery);
        return $flattery;
//        }
    }

    private function manipulateKarma($karma) {
        //TODO: increase/decrease User Karma und save User
        //      function needed that slowly approaches -1/1
    }
}