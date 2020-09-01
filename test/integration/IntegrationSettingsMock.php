<?php

use OLBotSettings\Model\Settings;

class IntegrationSettingsMock extends Settings {
    public function __construct($parser = null)
    {
        $this->parser = $parser;
    }
}