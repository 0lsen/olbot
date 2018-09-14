<?php

/**
 * @runTestsInSeparateProcesses
 */

class TestRouteTest extends FeatureTestCase
{
    function testRoute()
    {
        $this->mockKeywords();
        $this->mockFallbackAnswer(self::USER_ALLOWED);
        $this->client->post('/testing', $this->createMessage(self::USER_ALLOWED, self::USER_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
        $json = (string) $this->client->response->getBody();
        $this->assertJson($json);
        $this->assertEquals([
            'text' => 'does not compute',
            'logs' => ['In: foo bar']
        ], json_decode($json, true));
    }

    private function mockFallbackAnswer($chat)
    {
        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'does not compute']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 99])
            ->once()
            ->andReturn($answerBuilder);

        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"does not compute"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];
    }
}