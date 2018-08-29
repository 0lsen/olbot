<?php

/**
 * @runTestsInSeparateProcesses
 */

class KarmaTest extends FeatureTestCase
{
    private $keyWord;

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
                    strpos($body, $this->keyWord) !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        parent::setup();
    }

    function testActiveFlattery()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => 'foo sweetie bar'
            ]
        ];

        $this->keyWord = 'Sweetie';

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testActiveInsult()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => 'foo jerk bar'
            ]
        ];

        $this->keyWord = 'Jerk';

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveFlattery()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 123
                ],
                'text' => 'foo 1+1 bar'
            ]
        ];

        $this->keyWord = 'Sweetie';

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testPassiveInsult()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 456
                ],
                'text' => 'foo 1+1 bar'
            ]
        ];

        $this->keyWord = 'Jerk';

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}