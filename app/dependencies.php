<?php

$container = $app->getContainer();

$container['storage'] = function (\Psr\Container\ContainerInterface $c) use ($settings) {
    return new \OLBot\Service\StorageService($settings);
};

$container['message'] = function (\Psr\Container\ContainerInterface $c) use ($settings) {
    return new \OLBot\Service\MessageService($settings['token']);
};

$container['incoming'] = function (\Psr\Container\ContainerInterface $c) {
    return new \OLBot\Controller\IncomingController(
        $c['storage']
    );
};