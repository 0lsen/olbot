<?php

define('PROJECT_ROOT', __DIR__.'/..');
define('APP_ROOT', PROJECT_ROOT.'/app');

require_once PROJECT_ROOT.'/vendor/autoload.php';
require_once 'FeatureTestCase.php';
require_once 'feature/EloquentMock.php';