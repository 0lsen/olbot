<?php

namespace OLBot\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class InstantResponseMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        foreach ($this->storageService->settings['instant_responses'] as $instant) {
            if (preg_match($instant['regex'], $this->storageService->textCopy)) {
                $this->storageService->sendResponse = true;
                $this->storageService->response['main'][] = $instant['response'];

                if ($instant['break'])
                    return $response;
            }
        }

        return $next($request, $response);
    }
}