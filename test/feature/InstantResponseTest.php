<?php

/**
 * @runTestsInSeparateProcesses
 */

class InstantResponseTest extends FeatureTestCase
{
    function setup()
    {
        parent::expectMessage();
        parent::setup();
    }

    function testInstantResponseWithBreak()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"Matata"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'Hakuna'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    // TODO
    // function testInstantResponseWithoutBreak(){}
}