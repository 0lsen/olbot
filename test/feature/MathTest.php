<?php

/**
 * @runTestsInSeparateProcesses
 */

class MathTest extends FeatureTestCase
{
    function testMath()
    {
        $allowedUserMock = Mockery::mock('alias:OLBot\Model\DB\AllowedUser');
        $allowedUserMock
            ->shouldReceive('where')
            ->with(['id' => 123, 'active' => true])
            ->once()
            ->andReturn(new EloquentMock());

        $guzzleMock = Mockery::mock('overload:Guzzle\Http\Client');
        $guzzleMock
            ->shouldReceive('post')
            ->with(
                'https://api.telegram.org/botasd/sendMessage',
                ['Content-Type' => 'application/json'],
                ['chat_id' => 123, 'text' => '\nHello World!\n1+1 = 2']
            )
            ->once()
            ->andReturnTrue();

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