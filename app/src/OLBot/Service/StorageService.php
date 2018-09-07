<?php

namespace OLBot\Service;


use OLBot\Model\DB\AllowedUser;
use OLBot\Model\Reponse;
use OLBot\Model\SubjectCandidate;
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

    /** @var SubjectCandidate[] */
    public $subjectCandidates = [];

    public $sendResponse = false;
    public $response;
    public $karma;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->response = new Reponse();
    }
}