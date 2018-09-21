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

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'math 1 + 1 bar'));
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
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 2])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'Marcö bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

//    function testPictureResponse()
//    {
//        $from = self::USER_NEUTRAL_KARMA;
//        $chat = self::GROUP_ALLOWED;
//
//        $this->mockKeywords(['picture' => 3, 'bar' => null]);
//        $this->expectedMessageContent = [
//            'chat_id' => $chat,
//            'photo' => '"https:\/\/example.com\/picture.jpg"',
//            'reply_to_message_id' => self::MESSAGE_ID,
//        ];
//
//        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
//        $answerCollection->add((object) ['text' => 'https://example.com/picture.jpg']);
//        $answerBuilder = new BuilderMock($answerCollection);
//        $this->answerMock
//            ->shouldReceive('where')
//            ->with(['category' => 3])
//            ->once()
//            ->andReturn($answerBuilder);
//
//        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'picture bar'));
//        $this->assertEquals(200, $this->client->response->getStatusCode());
//    }

    public function testRequiredCategoryHitsPositive()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['categoryfourone' => 4, 'categoryfourtwo' => 4, 'categoryfive' => 5]);
        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"success"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'success']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 4])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'categoryfourone categoryfourtwo categoryfive'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    public function testRequiredCategoryHitsNegative()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['categoryfour' => 4, 'categoryfive' => 5]);
        $this->expectedMessageContent = [
            'chat_id' => $chat,
            'text' => '"fallback"',
            'reply_to_message_id' => self::MESSAGE_ID,
        ];

        $answerCollection = new \Illuminate\Database\Eloquent\Collection();
        $answerCollection->add((object) ['text' => 'fallback']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => \OLBot\Category\AbstractCategory::CAT_FALLBACK])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'categoryfour categoryfive'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}