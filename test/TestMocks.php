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

class BuilderMock
{
    private $return;

    public function __construct($return)
    {
        $this->return = $return;
    }

    public function __call($name, $arguments)
    {
        return $this->return;
    }
}