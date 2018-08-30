<?php

use Illuminate\Database\Eloquent\Collection;

class FeatureTestCase extends \There4\Slim\Test\WebTestCase
{
    protected const USER_ALLOWED = 1;
    protected const USER_NOT_ALLOWED = 2;
    protected const USER_POSITIVE_KARMA = 1;
    protected const USER_NEGATIVE_KARMA = 2;
    protected const USER_NEUTRAL_KARMA = 3;
    protected const GROUP_ALLOWED = -1;
    protected const GROUP_NOT_ALLOWED = -2;
    protected const MESSAGE_ID = 123;

    /** @var \Mockery\MockInterface */
    protected $karmaMock;

    protected $expectedMessageContent = [];

    function setup()
    {
        $this->mockAllowedUsersAndGroups();
        $this->mockKarma();
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

    private function mockAllowedUsersAndGroups()
    {
        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedUser');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_POSITIVE_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEGATIVE_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 0]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEUTRAL_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_POSITIVE_KARMA])
            ->andReturn(new EloquentMock(['karma' => 1, 'id' => self::USER_POSITIVE_KARMA]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEGATIVE_KARMA])
            ->andReturn(new EloquentMock(['karma' => -1, 'id' => self::USER_NEGATIVE_KARMA]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEUTRAL_KARMA])
            ->andReturn(new EloquentMock(['karma' => 0, 'id' => self::USER_NEUTRAL_KARMA]));

        $allowedGroupMock = Mockery::mock('alias:OLBot\Model\DB\AllowedGroup');
        $allowedGroupMock
            ->shouldReceive('where')
            ->with(['id' => self::GROUP_ALLOWED, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedGroupMock
            ->shouldReceive('where')
            ->with(['id' => self::GROUP_NOT_ALLOWED, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 0]));
    }

    private function mockKarma()
    {
        $karmaPositiveCollection = new Collection();
        $karmaPositiveCollection->add((object)['text' => 'sweetie', 'author' => null]);
        $karmaNegativeCollection = new Collection();
        $karmaNegativeCollection->add((object)['text' => 'jerk', 'author' => null]);

        $this->karmaMock = Mockery::mock('alias:OLBot\Model\DB\Karma');
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => true])
            ->andReturn($karmaPositiveCollection);
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => false])
            ->andReturn($karmaNegativeCollection);
    }

    protected function mockLogMessageIn()
    {
        $logMessageInMock = Mockery::mock('alias:OLBot\Model\DB\LogMessageIn');
        $logMessageInMock
            ->shouldReceive('create')
            ->once();
    }

    protected function createMessage($fromId, $chatId, $text = 'foo bar')
    {
        $message = new \Swagger\Client\Telegram\Message();
        $message->setMessageId(self::MESSAGE_ID);
        $message->setText($text);
        $chat = new \Swagger\Client\Telegram\Chat();
        $chat->setId($chatId);
        $message->setChat($chat);
        $from = new \Swagger\Client\Telegram\User();
        $from->setId($fromId);
        $message->setFrom($from);
        return ['message' => \Swagger\Client\ObjectSerializer::sanitizeForSerialization($message)];
    }

    protected function expectMessage()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $request){
                $body = $request->getBody()->getContents();
                $match = $request->getUri()->getPath() == '/botasd/sendMessage';
                foreach ($this->expectedMessageContent as $key => $value) {
                    if (strpos($body, '"'.$key.'": '.$value) === false) {
                        $match = false;
                        break;
                    }
                }
                return $match;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        $logMessageOutMock = Mockery::mock('alias:OLBot\Model\DB\LogMessageOut');
        $logMessageOutMock
            ->shouldReceive('create')
            ->once();
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