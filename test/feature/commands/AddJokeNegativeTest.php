<?php

/**
 * @runTestsInSeparateProcesses
 */

class AddJokeNegativeTest extends FeatureTestCase
{
    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $request){
                return
                    $request->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($request->getBody()->getContents(), 'I already know this.') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        $jokeMock = Mockery::mock('alias:OLBot\Model\DB\Joke');
        $jokeMock
            ->shouldReceive('where')
            ->with(['text' => 'foo joke'])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));

        parent::setup();
    }

    function testAddNewJoke()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 789
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => '/addJoke foo joke'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}