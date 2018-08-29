<?php

/**
 * @runTestsInSeparateProcesses
 */

class AddFlatteryNegativeTest extends FeatureTestCase
{
    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $request){
                $body = $request->getBody()->getContents();
                return
                    $request->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($body, '789') !== false &&
                    strpos($body, 'I already know this.') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();

        $this->karmaMock
            ->shouldReceive('where')
            ->with(['text' => 'foo flattery', 'karma' => true])
            ->once()
            ->andReturn(new EloquentMock(['count' => 1]));
    }

    function testAddNewFlatteryNegative()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 789
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => '/addFlattery foo flattery'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}