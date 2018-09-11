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
            $this->storageService->response->text[] = Answer::where(['category' => AbstractCategory::CAT_FALLBACK])->random()->text;
        }
        return $response;
    }
}