<?php

namespace OLBot\Controller;


use OLBot\Category\AbstractCategory;
use OLBot\Middleware\TextBasedMiddleware;
use OLBot\Model\DB\Answer;
use Slim\Http\Request;
use Slim\Http\Response;

class IncomingController extends TextBasedMiddleware
{
    function evaluate(Request $request, Response $response, $args)
    {
        if (!$this->storageService->sendResponse) {
            $this->storageService->sendResponse = true;
            $answers = Answer::where(['category' => AbstractCategory::CAT_FALLBACK]);
            if (!$answers->count()) throw new \Exception('no fallback answer found.');
            $this->storageService->response->text[] = $answers->inRandomOrder()->first()->text;
        }
        return $response;
    }
}