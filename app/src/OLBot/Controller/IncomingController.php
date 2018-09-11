<?php

namespace OLBot\Controller;


use OLBot\Middleware\TextBasedMiddleware;
use OLBot\Model\DB\Answer;
use Slim\Http\Request;
use Slim\Http\Response;

class IncomingController extends TextBasedMiddleware
{
    const FALLBACK_CATEGORY = 99;

    function evaluate(Request $request, Response $response, $args)
    {
        if (!$this->storageService->sendResponse) {
            $this->storageService->sendResponse = true;
            $this->storageService->response->text[] = Answer::where(['category' => self::FALLBACK_CATEGORY])->random()->text;
        }
        return $response;
    }
}