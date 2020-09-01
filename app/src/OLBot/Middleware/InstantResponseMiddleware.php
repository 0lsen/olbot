<?php

namespace OLBot\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class InstantResponseMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        foreach ($this->storageService->settings->getInstantResponses() as $instant) {
            if (preg_match($instant->getRegex(), $this->storageService->textCopy)) {
                $this->storageService->sendResponse = true;
                $this->storageService->response->text[] = $instant->getResponse();

                if ($instant->getBreak())
                    return $response;
            }
        }

        return $next($request, $response);
    }
}