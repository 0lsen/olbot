<?php

namespace OLBot\Middleware;


use Math\Parser;
use Math\Result;
use Slim\Http\Request;
use Slim\Http\Response;

class MathMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        Parser::init();
        $results = Parser::evaluate($this->storageService->message->getText());
        foreach ($results as $result) {
            $this->storageService->response['math'][] = $this->formatResult($result);
            $this->removeFromText($result->original);
        }

        if ($results) $this->storageService->sendResponse = true;

        return $next($request, $response);
    }

    private function formatResult(Result $result)
    {
        if ($result->dbz) {
            return $this->storageService->settings['math']['dbz_message'];
        } else {
            $string = $result->original . ' = ' . $result->result;
            return str_replace('.', $this->storageService->settings['math']['decimal_point'], $string);
        }
    }
}