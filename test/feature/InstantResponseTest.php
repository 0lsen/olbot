<?php

use Telegram\Model\ParseMode;
use Telegram\Model\SendMessageBody;

/**
 * @runTestsInSeparateProcesses
 */

class InstantResponseTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::expectMessage();
        parent::setup();
    }

    function testInstantResponseWithBreak()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'Matata',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'Hakuna'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    // TODO
    // function testInstantResponseWithoutBreak(){}
}