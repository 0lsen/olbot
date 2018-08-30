<?php

/**
 * @runTestsInSeparateProcesses
 */

class MathTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::expectMessage();
        parent::setup();
    }

    function testMath()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"1 + 1 = 2"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'foo 1 + 1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}