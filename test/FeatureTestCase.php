<?php

use Illuminate\Database\Eloquent\Collection;

class FeatureTestCase extends \There4\Slim\Test\WebTestCase
{
    /** @var \Mockery\MockInterface */
    protected $karmaMock;

    function setup()
    {
        $this->mockStuff();
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

    private function mockStuff()
    {
        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedUser');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 123, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 456, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 0]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 789, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 123])
            ->andReturn(new EloquentMock(['karma' => 1, 'id' => 123]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 456])
            ->andReturn(new EloquentMock(['karma' => -1, 'id' => 456]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 789])
            ->andReturn(new EloquentMock(['karma' => 0, 'id' => 789]));

        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedGroup');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => -123, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => -456, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 0]));

        $karmaPositiveCollection = new Collection();
        $karmaPositiveCollection->add((object)['text' => 'sweetie', 'author' => null]);
        $karmaNegativeCollection = new Collection();
        $karmaNegativeCollection->add((object)['text' => 'jerk', 'author' => null]);

        $this->karmaMock = Mockery::mock('alias:OLBot\Model\DB\Karma');
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => true])
            ->andReturn(new EloquentMock(['get' => $karmaPositiveCollection]));
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => false])
            ->andReturn(new EloquentMock(['get' => $karmaNegativeCollection]));
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