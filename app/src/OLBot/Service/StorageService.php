<?php

namespace OLBot\Service;


class StorageService
{
    public $settings;
    public $message;
    public $sendResponse = false;
    public $main;
    public $math;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }
}