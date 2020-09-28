<?php

use Illuminate\Database\Eloquent\Collection;
use OLBot\Category\AbstractCategory;
use OpenWeather\Configuration;
use OpenWeather\Model\Main;
use OpenWeather\Model\Model200;
use OpenWeather\Model\Sys;
use OpenWeather\Model\Weather;
use OpenWeather\Model\Wind;
use Telegram\Model\ParseMode;
use Telegram\Model\SendMessageBody;

//include_once '../bootstrap.php';
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

    function testMath($mock = true)
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['math' => 1, 'bar' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => '<code>1 + 1 = 2</code>',
            'parse_mode' => ParseMode::HTML,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        if ($mock) $this->mockMath('1 + 1', '2');

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'math 1 + 1 bar'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testWeather($mock = true)
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['how' => null, 'the' => null, 'weather' => 6]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'The weather in London (GB) is misty at 7.5 °C and 2.6 km/h wind.',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        if ($mock) $this->mockWeather();

        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'The weather in #place# is #situation# at #temp# °C and #wind# km/h wind.']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 6])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'How is the weather in London?'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    private function mockWeather()
    {
        $weatherResponse = new Model200();
        $weatherResponse->setName('London');
        $weatherResponse->setSys(new Sys(['country' => 'GB']));
        $weatherResponse->setMain(new Main(['temp' => 7.5]));
        $weatherResponse->setWeather([new Weather(['description' => 'misty'])]);
        $weatherResponse->setWind(new Wind(['speed' => 2.6]));

        $openweatherMock = Mockery::mock('overload:OpenWeather\Api\CurrentWeatherDataApi');
        $openweatherMock
            ->expects('getConfig')
            ->once()
            ->andReturn(new Configuration());
        $openweatherMock
            ->expects('currentWeatherData')
            ->with('London', null, null, null, null, 'metric', 'en')
            ->once()
            ->andReturn($weatherResponse);
    }

    function testTextResponse()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['marco' => 2, 'bar' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "polo\n    _-User Allowed_",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'polo', 'author' => self::USER_ALLOWED]);
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
//        $answerCollection = new Collection();
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
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "success\n    _-User Allowed_",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'success', 'author' => self::USER_ALLOWED]);
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

        $this->mockKeywords(['categoryfour' => 4, 'unknown' => null]);
        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => 'fallback',
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'fallback']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => AbstractCategory::CAT_FALLBACK])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'categoryfour unknown'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    public function testLatestTextResponse()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['latest' => AbstractCategory::CAT_LATEST, 'categorytwo' => 2]);

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "latest",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['time' => '2018-01-01 01:00:00', 'text' => 'first', 'author' => '']);
        $answerCollection->add((object) ['time' => '2018-01-01 02:00:00', 'text' => 'latest', 'author' => '']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 2])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'categorytwo latest'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    public function testLearningTextResponsePositive()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['momma' => 5, 'fat' => 5, 'sooo' => null]);

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "yo momma supa fat",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['time' => '2018-01-01 01:00:00', 'text' => 'yo momma supa fat', 'author' => '']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 5])
            ->once()
            ->andReturn($answerBuilder);
        $this->answerMock
            ->shouldReceive('create')
            ->with(['category' => 5, 'text' => 'yo momma sooo fat', 'author' => 5])
            ->once();

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'yo momma sooo fat'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    public function testLearningTextResponseNegativeAnswerAlreadyKnown()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['momma' => 5, 'fat' => 5]);

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "yo momma so fat",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['time' => '2018-01-01 01:00:00', 'text' => 'yo momma so fat', 'author' => '']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 5])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'yo momma so fat'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    public function testLearningTextResponseNegativeKeywordsOnly()
    {
        $from = self::USER_NEUTRAL_KARMA;
        $chat = self::GROUP_ALLOWED;

        $this->mockKeywords(['momma' => 5, 'fat' => 5]);

        $this->expectedMessage = new SendMessageBody([
            'chat_id' => $chat,
            'text' => "yo momma so fat",
            'parse_mode' => ParseMode::MARKDOWN,
            'reply_to_message_id' => self::MESSAGE_ID,
        ]);

        $answerCollection = new Collection();
        $answerCollection->add((object) ['time' => '2018-01-01 01:00:00', 'text' => 'yo momma so fat', 'author' => '']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => 5])
            ->once()
            ->andReturn($answerBuilder);

        $this->client->post('/incoming', $this->createMessageUpdate($from, $chat, 'MomMa Fat'));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}