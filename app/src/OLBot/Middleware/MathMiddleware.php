<?php

namespace OLBot\Middleware;


use Math\Parser;
use Math\Result;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;

class MathMiddleware
{
    /** @var StorageService */
    private $storageService;

    public function __construct($storageService)
    {
        $this->storageService = $storageService;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        Parser::init();
        $results = Parser::evaluate($this->storageService->message['message']['text']);
        foreach ($results as $result) {
            $this->storageService->math[] = $this->formatResult($result);
        }

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