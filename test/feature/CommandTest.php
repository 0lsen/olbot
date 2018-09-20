<?php

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

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"I already know this."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addFlattery foo bar'));
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

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"Thank you for your contribution."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addFlattery foo bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewInsultNegative()
    {
        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'karma' => false])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"I already know this."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addInsult foo bar'));
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

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"Thank you for your contribution."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addInsult foo bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewJokeNegative()
    {
        $jokeMock = Mockery::mock('alias:OLBot\Model\DB\Joke');
        $jokeMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar'])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"I already know this."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addJoke foo bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddNewJokePositive()
    {
        $jokeMock = Mockery::mock('alias:OLBot\Model\DB\Joke');
        $jokeMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar'])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $jokeMock
            ->shouldReceive('create')
            ->with(['text' => 'foo bar', 'author' => $this->chat])
            ->once();

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"Thank you for your contribution."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addJoke foo bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddSomethingWithNoArgument()
    {
        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"ERROR: not enough parameters."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addJoke'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testAddCategoryAnswer()
    {
        $this->answerMock
            ->shouldReceive('where')
            ->with(['text' => 'foo bar', 'category' => 1])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $this->answerMock
            ->shouldReceive('create')
            ->with(['text' => 'foo bar', 'author' => $this->chat, 'category' => 1])
            ->once();

        $this->expectedMessageContent = [
            'chat_id' => $this->chat,
            'text' => '"Thank you for your contribution."',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($this->chat, $this->chat, '/addCategoryAnswer foo bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}