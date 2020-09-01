<?php

$app = new \Slim\App(require PROJECT_ROOT . '/app/config/slim.php');

$settingsArray = require PROJECT_ROOT . '/app/config/olbot.php';
/** @var \OLBotSettings\Model\Settings $settings */
$settings = \OLBotSettings\ObjectSerializer::deserialize(json_decode(json_encode($settingsArray)), 'OLBotSettings\Model\Settings');

require 'dependencies.php';
require 'routes.php';
require 'middleware.php';

$app->run();