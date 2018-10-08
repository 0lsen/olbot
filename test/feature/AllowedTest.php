<?php

use Illuminate\Database\Eloquent\Collection;
use OLBot\Category\AbstractCategory;
use Swagger\Client\Telegram\ParseMode;
use Swagger\Client\Telegram\SendMessageBody;

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
        $this->client->post('/incoming', $this->createMessageUpdate(self::USER_ALLOWED, self::USER_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testGroupPositive()
    {
        $this->mockKeywords();
        $this->mockFallbackAnswer(self::GROUP_ALLOWED);
        $this->client->post('/incoming', $this->createMessageUpdate(self::USER_NOT_ALLOWED, self::GROUP_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    private function mockFallbackAnswer($chat)
    {
        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'does not compute']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => AbstractCategory::CAT_FALLBACK])
            ->once()
            ->andReturn($answerBuilder);

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'does not compute',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);
    }
}