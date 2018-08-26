<?php

/**
 * @runTestsInSeparateProcesses
 */

class AddInsultNegativeTest extends FeatureTestCase
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

        parent::setup();

        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo insult', 'karma' => false])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));
    }

    function testAddNewFlattery()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 789
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => '/addInsult foo insult'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}