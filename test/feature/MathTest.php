<?php

///**
// * @runTestsInSeparateProcesses
// */

class MathTest extends FeatureTestCase
{
    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $response){
                return
                    $response->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($response->getBody()->getContents(), '1 + 1  = 2') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();
    }

    function testMath()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 123
                ],
                'text' => 'foo 1 + 1 bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}