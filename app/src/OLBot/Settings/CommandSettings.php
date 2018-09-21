<?php

namespace OLBot\Settings;


class CommandSettings
{
    public $name;
    public $settings;

    public function __construct(string $name, array $settings)
    {
        $this->name = $name;
        $this->settings = $settings;
    }
}