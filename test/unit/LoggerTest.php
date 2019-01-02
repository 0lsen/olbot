<?php

use Telegram\Model\Message;
use Telegram\Model\SendMessageBody;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    function testLogError()
    {
        $mock = Mockery::mock('alias:OLBot\Model\DB\LogError');
        $mock
            ->shouldReceive('create')
            ->withArgs(function ($args) {
                $this->assertEquals(123, $args['id_in']);
                $this->assertContains('1 - error [', $args['message']);
                return true;
            })
            ->once();

        \OLBot\Logger::logError(123, new Exception('error', 1));

        Mockery::close();
    }

    function testLogMessageIn()
    {
        $mock = Mockery::mock('alias:OLBot\Model\DB\LogMessageIn');
        $mock
            ->shouldReceive('create')
            ->withArgs(function ($args) {
                $this->assertEquals(123, $args['id_in']);
                $this->assertContains('foo bar', $args['content']);
                return true;
            })
            ->once();

        \OLBot\Logger::logMessageIn(new Message(['text' => 'foo bar', 'message_id' => 123]));

        Mockery::close();
    }

    function testLogMessageOut()
    {
        $mock = Mockery::mock('alias:OLBot\Model\DB\LogMessageOut');
        $mock
            ->shouldReceive('create')
            ->withArgs(function ($args) {
                $this->assertEquals(123, $args['id_in']);
                $this->assertContains('foo bar', $args['content']);
                return true;
            })
            ->once();

        \OLBot\Logger::logMessageOut(new SendMessageBody(['text' => 'foo bar', 'reply_to_message_id' => 123]));

        Mockery::close();
    }
}