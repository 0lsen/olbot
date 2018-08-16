<?php

namespace OLBot\Middleware;


use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;

class BotmasterMiddleware
{
    private $storageService;

    function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $this->storageService->botmaster = $this->storageService->message->getChat()->getId() == $this->storageService->settings['botmaster_id'];
    }
}