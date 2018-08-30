<?php

/**
 * @runTestsInSeparateProcesses
 */

class AllowedTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::setup();
    }

    function testUserPositive()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_ALLOWED, self::USER_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testUserNegative()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_NOT_ALLOWED, self::USER_NOT_ALLOWED));
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }

    function testGroupPositive()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_NOT_ALLOWED, self::GROUP_ALLOWED));
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function testGroupNegative()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_ALLOWED, self::GROUP_NOT_ALLOWED));
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }
}