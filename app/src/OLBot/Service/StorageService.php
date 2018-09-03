<?php

namespace OLBot\Service;


use OLBot\Model\DB\AllowedUser;
use OLBot\Settings;
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
    public $karma;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
}