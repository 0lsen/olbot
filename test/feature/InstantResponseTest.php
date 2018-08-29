<?php

/**
 * @runTestsInSeparateProcesses
 */

class InstantResponseTest extends FeatureTestCase
{
    private $expectMath;

    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $request){
                $body = $request->getBody()->getContents();
                return
                    $request->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($body, '-123') !== false &&
                    strpos($body, 'Matata') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();
    }

    function testInstantResponseWithBreak()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => 'Hakuna'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    // TODO
    // function testInstantResponseWithoutBreak(){}
}