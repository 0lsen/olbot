<?php

use Swagger\Client\Telegram\ParseMode;
use Swagger\Client\Telegram\SendMessageBody;

/**
 * @runTestsInSeparateProcesses
 */

class KarmaTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::expectMessage();
        parent::setup();
    }

    function testActiveFlattery()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['foo' => null, 'bar' => null, 'sweetie' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'Sweetie',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'foo sweetie bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testActiveInsult()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['foo' => null, 'bar' => null, 'jerk' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'Jerk',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'foo jerk bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveFlattery()
    {
        $from = self::USER_POSITIVE_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['math' => 1, 'bar' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "<code>1+1 = 2</code>\nSweetie",
            'parse_mode' => ParseMode::HTML,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'math 1+1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveInsult()
    {
        $from = self::USER_NEGATIVE_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['math' => 1, 'bar' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "<code>1+1 = 2</code>\nJerk",
            'parse_mode' => ParseMode::HTML,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'math 1+1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}