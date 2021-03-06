<?php

namespace OLBot\Service;


use OLBot\Logger;
use Telegram\Api\AttachmentsApi;
use Telegram\Api\MessagesApi;
use Telegram\Model\ParseMode;
use Telegram\Model\SendMessageBody;
use Telegram\Model\SendPhotoLinkBody;

class MessageService
{
    static $parseMode = ParseMode::MARKDOWN;

    private $messagesApi;
    private $attachmentApi;
    private $token;

    function __construct($token)
    {
        $this->messagesApi = new MessagesApi();
        $this->attachmentApi = new AttachmentsApi();
        $this->token = $token;
    }

    function sendMessage($text, $idOut, $idIn, $asReply = true)
    {
        $message = new SendMessageBody();
        $message->setChatId($idOut);
        if ($asReply) $message->setReplyToMessageId($idIn);
        $message->setText($text);
        $message->setParseMode(self::$parseMode);
        try {
            $this->messagesApi->sendMessage($this->token, $message);
            Logger::logMessageOut($message);
        } catch (\Throwable $t) {
            Logger::logError($idIn, $t);
        }
    }

    function sendPicture($url, $idOut, $idIn)
    {
        // Todo: write test
        $message = new SendPhotoLinkBody();
        $message->setChatId($idOut);
        $message->setReplyToMessageId($idIn);
        $message->setPhoto($url);
        try {
            $this->attachmentApi->sendPhotoLink($this->token, $message);
            Logger::logMessageOut($message);
        } catch (\Throwable $t) {
            Logger::logError($idIn, $t);
        }
    }
}