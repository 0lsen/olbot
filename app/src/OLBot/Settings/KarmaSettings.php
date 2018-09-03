<?php

namespace OLBot\Settings;


class KarmaSettings
{
    public $step;
    public $function;

    public function __construct(float $step, string $function)
    {
        $this->step = $step;
        $this->function = $function;
    }
}