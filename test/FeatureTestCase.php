<?php

class FeatureTestCase extends \There4\Slim\Test\WebTestCase
{
    function getSlimInstance()
    {
        $app = new \Slim\App();

        $settings = require PROJECT_ROOT.'/config/olbot_test.php';

        require_once APP_ROOT.'/dependencies.php';
        require_once APP_ROOT.'/routes.php';
        require_once APP_ROOT.'/middleware.php';

        return $app;
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}