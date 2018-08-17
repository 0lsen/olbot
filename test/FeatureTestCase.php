<?php

class FeatureTestCase extends \There4\Slim\Test\WebTestCase
{
    function setup()
    {
        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedUser');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 123, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));

        $insultCollection = new \Illuminate\Database\Eloquent\Collection();
        $insultCollection->add((object)['insult' => 'jerk', 'author' => null]);
        $insultMock = Mockery::mock('alias:OLBot\Model\DB\Insult');
        $insultMock
            ->shouldReceive('all')
            ->andReturn($insultCollection);

        parent::setup();
    }

    function getSlimInstance()
    {
        $app = new \Slim\App();

        $settings = require PROJECT_ROOT . '/app/config/olbot_test.php';

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

class EloquentMock
{
    private $mockData;

    public function __construct($mockData = null)
    {
        $this->mockData = $mockData;
    }

    function __get($name)
    {
        return $this->mockData[$name];
    }

    function __call($name, $arguments)
    {
        return $this->mockData[$name] ?? 1;
    }
}