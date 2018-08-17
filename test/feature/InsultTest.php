<?php

/**
 * @runTestsInSeparateProcesses
 */

class InsultTest extends FeatureTestCase
{
    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $response){
                return
                    $response->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($response->getBody()->getContents(), 'Jerk') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();
    }

    function testInsult()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 123
                ],
                'text' => 'foo jerk bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}