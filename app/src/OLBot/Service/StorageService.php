<?php

namespace OLBot\Service;


use OLBot\Model\DB\AllowedUser;
use Swagger\Client\Telegram\Message;

class StorageService
{
    public $botmaster = false;
    public $settings;
    /** @var Message */
    public $message;
    public $textCopy;

    /** @var AllowedUser */
    public $user;

    public $sendResponse = false;
    public $response = ['main' => [], 'math' => []];
    public $karma = null;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }
}