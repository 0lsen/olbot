<?php

/**
 * @runTestsInSeparateProcesses
 */

class AllowedTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::expectMessage();
        parent::setup();
    }

    function testUserPositive()
    {
        $this->mockKeywords();
        $this->mockFallbackAnswer(self::USER_ALLOWED);
        $this->client->post('/incoming', $this->createMessage(self::USER_ALLOWED, self::USER_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testGroupPositive()
    {
        $this->mockKeywords();
        $this->mockFallbackAnswer(self::GROUP_ALLOWED);
        $this->client->post('/incoming', $this->createMessage(self::USER_NOT_ALLOWED, self::GROUP_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    private function mockFallbackAnswer($chat)
    {
        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'does not compute']);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 99])
            ->once()
            ->andReturn($answerCollection);

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"does not compute"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];
    }
}