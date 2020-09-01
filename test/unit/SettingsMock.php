<?php

use OLBotSettings\Model\Settings;

class SettingsMock extends Settings {
    public function __construct($parser = null)
    {
        $this->parser = $parser;
    }
}