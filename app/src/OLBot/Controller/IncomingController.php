<?php

namespace OLBot\Controller;


use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;

class IncomingController
{
    private $storageService;

    function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    function evaluate(Request $request, Response $response, $args)
    {
        return $response;
    }
}