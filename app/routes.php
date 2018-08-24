<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/healthcheck', function(Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
    return $response;
});

$app->post('/incoming', 'incoming:evaluate')
    ->add(new \OLBot\Middleware\KarmaMiddleware($container['storage']))
    ->add(new \OLBot\Middleware\MathMiddleware($container['storage']))
    ->add(new \OLBot\Middleware\AllowedMiddleware($container['storage']))
    ->add(new \OLBot\Middleware\MessageMiddleware($container['storage'], $container['message']));