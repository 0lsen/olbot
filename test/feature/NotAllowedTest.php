<?php

/**
 * @runTestsInSeparateProcesses
 */

class NotAllowedTest extends FeatureTestCase
{
    function setup()
    {
        parent::mockLogMessageIn();
        parent::setup();
    }

    function testUserNegative()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_NOT_ALLOWED, self::USER_NOT_ALLOWED));
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }

    function testGroupNegative()
    {
        $this->client->post('/incoming', $this->createMessage(self::USER_ALLOWED, self::GROUP_NOT_ALLOWED));
        $this->assertEquals(403, $this->client->response->getStatusCode());
    }
}