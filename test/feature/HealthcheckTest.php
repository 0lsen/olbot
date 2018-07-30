<?php

/**
 * @runTestsInSeparateProcesses
 */

class HealthcheckTest extends FeatureTestCase
{
    function testHealthCheck()
    {
        $result = $this->client->get('/healthcheck');

        $this->assertEquals(200, $this->client->response->getStatusCode());
        $this->assertEquals('OK', $result);
    }

    function testUnknownRoute()
    {
        $this->client->get('/unknown');
        $body = $this->client->response->getBody();
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
}