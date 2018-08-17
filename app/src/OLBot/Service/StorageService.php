<?php

namespace OLBot\Service;


use Swagger\Client\Telegram\Message;

class StorageService
{
    public $botmaster = false;
    public $settings;
    /** @var Message */
    public $message;
    public $sendResponse = false;
    public $response = ['main' => [], 'math' => []];
    public $insult = null;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }
}