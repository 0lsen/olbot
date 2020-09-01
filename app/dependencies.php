<?php

$container = $app->getContainer();

$container['storage'] = function (\Psr\Container\ContainerInterface $c) use ($settings) {
    return new \OLBot\Service\StorageService($settings);
};

$container['cache'] = function (\Psr\Container\ContainerInterface $c) use ($settings) {
    return new \OLBot\Service\CacheService($settings->getCache());
};

$container['message'] = function (\Psr\Container\ContainerInterface $c) use ($settings) {
    return new \OLBot\Service\MessageService($settings->getToken());
};

$container['incoming'] = function (\Psr\Container\ContainerInterface $c) {
    return new \OLBot\Controller\IncomingController(
        $c['storage'],
        $c['cache']
    );
};