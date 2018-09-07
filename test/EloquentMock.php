<?php

class EloquentMock
{
    private $mockData;

    public function __construct($mockData = null)
    {
        $this->mockData = $mockData;
    }

    function __get($name)
    {
        return $this->mockData[$name];
    }

    function __call($name, $arguments)
    {
        return $this->mockData[$name] ?? 1;
    }
}