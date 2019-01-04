<?php

class SettingsMock extends \OLBot\Settings {
    public function __construct($parser = null)
    {
        $this->parser = $parser;
    }
}