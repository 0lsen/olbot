<?php

/**
 * @runTestsInSeparateProcesses
 */

class ParserTest extends FeatureTestCase
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

        $this->mockKeywords(['math' => 1, 'bar' => null]);
        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"1 + 1 = 2"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'math 1 + 1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testTextResponse()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['marco' => 2, 'bar' => null]);
        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"polo"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'polo']);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 2])
            ->once()
            ->andReturn($answerCollection);

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'marco bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPictureResponse()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['picture' => 3, 'bar' => null]);
        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'photo' => '"https:\/\/example.com\/picture.jpg"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'https://example.com/picture.jpg']);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 3])
            ->once()
            ->andReturn($answerCollection);

        $this->client->post('/incoming', $this->createMessage($from, $chat, 'picture bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}