<?php

namespace OLBot\Settings;


class CommandSettings
{
    public $call;
    public $name;
    public $settings;

    public function __construct(string $call, string $name, array $settings)
    {
        $this->call = $call;
        $this->name = $name;
        $this->settings = $settings;
    }
}