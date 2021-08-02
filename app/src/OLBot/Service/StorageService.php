<?php

namespace OLBot\Service;


use OLBot\Model\DB\AllowedUser;
use OLBot\Model\Response;
use OLBot\Model\SubjectCandidate;
use OLBotSettings\Model\Settings;
use Telegram\Model\Message;

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

    public $authorHint;

    public $sendResponse = false;
    public $response;
    public $karma;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->response = new Response();
    }
}