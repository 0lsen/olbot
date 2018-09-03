<?php

namespace OLBot\Settings;


class MathSettings
{
    public $decimalPoint;
    public $divisionByZeroResponse;
    
    public function __construct(string $decimalPoint, string $divisionByZeroResponse)
    {
        $this->decimalPoint = $decimalPoint;
        $this->divisionByZeroResponse = $divisionByZeroResponse;
    }
}