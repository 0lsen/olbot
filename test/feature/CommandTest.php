<?php

use Illuminate\Database\Eloquent\Collection;
use Telegram\Model\MessageEntity;
use Telegram\Model\ParseMode;
use Telegram\Model\SendMessageBody;
use Telegram\Model\Update;
use Telegram\ObjectSerializer;

//include_once '../bootstrap.php';
/**
 * @runTestsInSeparateProcesses
 */

class CommandTest extends FeatureTestCase
{
    private $chat = self::USER_NEUTRAL_KARMA;

    function setup()
    {
        parent::mockLogMessageIn();
        parent::expectMessage();
        parent::setup();
    }

    function testAddNewFlatteryNegative()
    {
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'karma' => true])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'I already know this.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'addFlattery'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewFlatteryPositive()
    {
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'karma' => true])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $this->karmaMock
            ->shouldReceive('create')
            ->with(['text' => 'foo bar', 'author' => $this->chat, 'karma' => true])
            ->once();

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'Thank you for your contribution.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'addFlattery'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewInsultNegative()
    {
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'karma' => false])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'I already know this.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'addInsult'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewInsultPositive()
    {
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'karma' => false])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $this->karmaMock
            ->shouldReceive('create')
            ->with(['text' => 'foo bar', 'author' => $this->chat, 'karma' => false])
            ->once();

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'Thank you for your contribution.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'addInsult'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddSomethingWithNotEnoughArguments()
    {
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'ERROR: not enough parameters.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'category2Arguments', 'asd'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testGetSomething()
    {
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'answer',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'answer']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 2])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'categoryAnswer', ''));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddCategoryAnswerPositive()
    {
        $this->answerMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'category' => 2])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $this->answerMock
            ->shouldReceive('create')
            ->with(['text' => 'foo bar', 'author' => $this->chat, 'category' => 2])
            ->once();

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'Thank you for your contribution.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'categoryAnswer'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddCategoryAnswerNegative()
    {
        $this->answerMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'category' => 2])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'I already know this.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'categoryAnswer'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddCategoryPicture()
    {
        $this->answerMock
            ->shouldReceive('where')
            ->with(['text' => 'https://example.com/picture.jpg', 'category' => 1])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $this->answerMock
            ->shouldReceive('create')
            ->with(['text' => 'https://example.com/picture.jpg', 'author' => $this->chat, 'category' => 1])
            ->once();

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $this->chat,
            'text' => 'Thank you for your contribution.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createCommandUpdate($this->chat, $this->chat, 'categoryPicture', 'https://example.com/picture.jpg'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    private function createCommandUpdate($fromId, $chatId, $command, $text = 'foo bar')
    {
        $entity = new MessageEntity();
        $entity->setType(MessageEntity::TYPE_BOT_COMMAND);
        $entity->setOffset(0);
        $entity->setLength(strlen($command)+1);

        $message = $this->createMessage($fromId, $chatId, '/' . $command . ' ' . $text);
        $message->setEntities([$entity]);

        $update = new Update();
        $update->setMessage($message);

        return ObjectSerializer::sanitizeForSerialization($update);
    }
}