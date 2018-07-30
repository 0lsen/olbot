<?php

namespace OLBot\Controller;


use Slim\Http\Request;
use Slim\Http\Response;

class IncomingControler
{
    private $storageService;

    function __construct($storageService)
    {
        $this->storageService = $storageService;
    }

    function evaluate(Request $request, Response $response, $args)
    {
        $this->storageService->main[] = 'Hello World!';
        $this->storageService->sendResponse = true;

        return $response;
    }
}