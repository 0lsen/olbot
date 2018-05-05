<?php

define('PROJECT_ROOT', __DIR__ .'/..');

require PROJECT_ROOT.'/vendor/autoload.php';

$settings = require PROJECT_ROOT.'/config/slim.php';
$app = new \Slim\App($settings);

require '../app/bootstrap.php';

$app->run();