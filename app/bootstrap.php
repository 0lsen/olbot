<?php

$app = new \Slim\App(require PROJECT_ROOT.'/config/slim.php');

$settings = require PROJECT_ROOT.'/config/olbot.php';

require 'dependencies.php';
require 'routes.php';
require 'middleware.php';

$app->run();