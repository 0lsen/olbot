<?php
include_once 'TestMocks.php';

use Illuminate\Database\Eloquent\Collection;
use PHPythagoras\Configuration;
use PHPythagoras\Model\FormulaResponseBody;
use Telegram\Model\Chat;
use Telegram\Model\Message;
use Telegram\Model\MessageEntity;
use Telegram\Model\SendMessageBody;
use Telegram\Model\Update;
use Telegram\Model\User;
use Telegram\ObjectSerializer;

class FeatureTestCase extends \There4\Slim\Test\WebTestCase
{
    const USER_ALLOWED = 1;
    const USER_NOT_ALLOWED = 2;
    const USER_POSITIVE_KARMA = 3;
    const USER_NEGATIVE_KARMA = 4;
    const USER_NEUTRAL_KARMA = 5;
    const GROUP_ALLOWED = -1;
    const GROUP_NOT_ALLOWED = -2;
    const MESSAGE_ID = 123;

    const BOT_NAME = 'olbot';

    /** @var \Mockery\MockInterface */
    protected $karmaMock;
    /** @var \Mockery\MockInterface */
    protected $keywordMock;
    /** @var \Mockery\MockInterface */
    protected $answerMock;

    protected $token;
    /** @var SendMessageBody */
    protected $expectedMessage;

    function setup()
    {
        $this->mockAllowedUsersAndGroups();
        $this->mockKarma();
        $this->keywordMock = Mockery::mock('alias:OLBot\Model\DB\Keyword');
        $this->answerMock = Mockery::mock('alias:OLBot\Model\DB\Answer');

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
            ->with(['id' => self::USER_ALLOWED, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NOT_ALLOWED, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 0]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_POSITIVE_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEGATIVE_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => self::USER_NEUTRAL_KARMA, 'active' => true])
            ->andReturn(new EloquentMock(['count' => 1]));

        $allowedUserMock
            ->shouldReceive('find')
            ->with(self::USER_ALLOWED)
            ->andReturn(new EloquentMock(['name' => 'User Allowed', 'karma' => 0, 'id' => self::USER_ALLOWED]));
        $allowedUserMock
              ->shouldReceive('find')
            ->with(self::USER_NOT_ALLOWED)
            ->andReturn(new EloquentMock(['name' => 'User Not Allowed', 'karma' => 0, 'id' => self::USER_NOT_ALLOWED]));
        $allowedUserMock
            ->shouldReceive('find')
            ->with(self::USER_POSITIVE_KARMA)
            ->andReturn(new EloquentMock(['name' => 'User Positive Karma', 'karma' => 1, 'id' => self::USER_POSITIVE_KARMA]));
        $allowedUserMock
            ->shouldReceive('find')
            ->with(self::USER_NEGATIVE_KARMA)
            ->andReturn(new EloquentMock(['name' => 'User Negative Karma', 'karma' => -1, 'id' => self::USER_NEGATIVE_KARMA]));
        $allowedUserMock
            ->shouldReceive('find')
            ->with(self::USER_NEUTRAL_KARMA)
            ->andReturn(new EloquentMock(['name' => 'User Neutral Karma', 'karma' => 0, 'id' => self::USER_NEUTRAL_KARMA]));

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

        $karmaPositiveBuilder = new BuilderMock($karmaPositiveCollection);
        $karmaNegativeBuilder = new BuilderMock($karmaNegativeCollection);

        $this->karmaMock = Mockery::mock('alias:OLBot\Model\DB\Karma');
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => true])
            ->andReturn($karmaPositiveBuilder);
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['karma' => false])
            ->andReturn($karmaNegativeBuilder);
    }

    protected function mockKeywords($words = ['foo' => null, 'bar' => null])
    {
        foreach ($words as $index => $category) {
            $this->keywordMock
                ->shouldReceive('find')
                ->with(md5($index))
                ->once()
                ->andReturn(is_null($category) ? null : new EloquentMock(['category' => $category]));
        }
    }

    protected function mockLogMessageIn()
    {
        $logMessageInMock = Mockery::mock('alias:OLBot\Model\DB\LogMessageIn');
        $logMessageInMock
            ->shouldReceive('create')
            ->once();
    }

    protected function createMessageUpdate($fromId, $chatId, $text = 'foo bar')
    {
        $message = $this->createMessage($fromId, $chatId, $text);

        $update = new Update();
        $update->setMessage($message);

        return ObjectSerializer::sanitizeForSerialization($update);
    }

    protected function createMessage($fromId, $chatId, $text) : Message
    {
        $message = new Message();
        $message->setMessageId(self::MESSAGE_ID);
        $message->setText(($chatId < 0 ? '@' . self::BOT_NAME . ' ' : '') . $text);
        $chat = new Chat();
        $chat->setId($chatId);
        $message->setChat($chat);
        $from = new User();
        $from->setId($fromId);
        $message->setFrom($from);

        if ($chatId < 0) {
            $entity = new MessageEntity();
            $entity->setType(MessageEntity::TYPE_MENTION);
            $entity->setOffset(0);
            $entity->setLength(strlen(self::BOT_NAME)+1);
            $message->setEntities([$entity]);
        }

        return $message;
    }

    protected function expectMessage()
    {
        $guzzleMock = Mockery::mock('overload:Telegram\Api\MessagesApi');
        $guzzleMock
            ->shouldReceive('sendMessage')
            ->withArgs(function ($token, SendMessageBody $message) {
                $this->assertEquals($this->expectedMessage, $message);
                return true;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        $logMessageOutMock = Mockery::mock('alias:OLBot\Model\DB\LogMessageOut');
        $logMessageOutMock
            ->shouldReceive('create')
            ->once();
    }

    protected function mockMath($formula, $result)
    {
        $mathResponse = new FormulaResponseBody();
        $mathResponse->setOk(true);
        $mathResponse->setResultString($formula . ' = ' . $result);

        $phpythagorasMock = Mockery::mock('overload:PHPythagoras\Api\DefaultApi');
        $phpythagorasMock
            ->expects('getConfig')
            ->once()
            ->andReturn(new Configuration());
        $phpythagorasMock
            ->expects('formulaEvaluateFulltextPost')
            ->once()
            ->andReturn($mathResponse);
    }
}