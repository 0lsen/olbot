<?php

use Illuminate\Database\Eloquent\Collection;
use OLBot\Category\AbstractCategory;

/**
 * @runTestsInSeparateProcesses
 */

class TestRouteTest extends FeatureTestCase
{
    function testRoute()
    {
        $this->mockKeywords();
        $this->mockFallbackAnswer();
        $this->client->post('/testing', $this->createMessageUpdate(self::USER_ALLOWED, self::USER_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
        $json = (string) $this->client->response->getBody();
        $this->assertJson($json);
        $this->assertEquals([
            'logs' => ['In: foo bar'],
            'storage' => [
                'karma' => null,
                'response' => [
                    'pics' => [],
                    'text' => ['does not compute']
                ],
                'sendResponse' => true,
                'subjectCandidates' => []
            ]
        ], json_decode($json, true));
    }

    private function mockFallbackAnswer()
    {
        $answerCollection = new Collection();
        $answerCollection->add((object) ['text' => 'does not compute']);
        $answerBuilder = new BuilderMock($answerCollection);
        $this->answerMock
            ->shouldReceive('where')
            ->with(['category' => AbstractCategory::CAT_FALLBACK])
            ->once()
            ->andReturn($answerBuilder);
    }
}