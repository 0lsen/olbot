<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/healthcheck', function(Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
    return $response;
});

$app->get('/test', function(Request $request, Response $response, array $args) {
    $response->getBody()->write(" - Test Route Main Function");
})
    ->add(new \OLBot\Middleware\DBLogIncomingMiddleware())
    ->add(new \OLBot\Middleware\DBConnectMiddleware());