<?php

$app = new \Slim\App(require PROJECT_ROOT . '/app/config/slim.php');

$settings = require PROJECT_ROOT . '/app/config/olbot.php';

require 'dependencies.php';
require 'routes.php';
require 'middleware.php';

$app->run();