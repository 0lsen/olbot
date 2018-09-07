<?php

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

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"Sweetie"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'foo sweetie bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testActiveInsult()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"Jerk"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'foo jerk bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveFlattery()
    {
        $from = self::USER_POSITIVE_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"1+1 = 2\\\\nSweetie"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'math 1+1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveInsult()
    {
        $from = self::USER_NEGATIVE_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"1+1 = 2\\\\nJerk"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'math 1+1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}