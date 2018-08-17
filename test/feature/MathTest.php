<?php

/**
 * @runTestsInSeparateProcesses
 */

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
                    strpos($response->getBody()->getContents(), '1+1 = 2') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();
    }

    function testMath()
    {
        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedUser');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 123, 'active' => true])
            ->once()
            ->andReturn(new EloquentMock());

        $request = [
            'message' => [
                'chat' => [
                    'id' => 123
                ],
                'text' => '1 + 1'
            ]
        ];

        $result = $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}