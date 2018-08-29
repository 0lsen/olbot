<?php

/**
 * @runTestsInSeparateProcesses
 */

class AllowedTest extends FeatureTestCase
{
    function testUserPositive()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 123
                ],
                'from' => [
                    'id' => 123
                ],
                'text' => 'foo bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testUserNegative()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 456
                ],
                'from' => [
                    'id' => 456
                ],
                'text' => 'foo bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }

    function testGroupPositive()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -123
                ],
                'from' => [
                    'id' => 456
                ],
                'text' => 'foo bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testGroupNegative()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => -456
                ],
                'from' => [
                    'id' => 456
                ],
                'text' => 'foo bar'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }
}