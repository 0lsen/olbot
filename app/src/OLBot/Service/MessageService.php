<?php

namespace OLBot\Service;


use OLBot\Logger;
use Swagger\Client\Api\AttachmentsApi;
use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\ParseMode;
use Swagger\Client\Telegram\SendMessageBody;
use Swagger\Client\Telegram\SendPhotoLinkBody;

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

    function sendMessage($text, $idOut, $idIn)
    {
        $message = new SendMessageBody();
        $message->setChatId($idOut);
        $message->setReplyToMessageId($idIn);
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
        $message = new SendPhotoLinkBody();
        $message->setChatId($idOut);
        $message->setReplyToMessageId($idIn);
        $message->setPhoto($url);
        try {
            // Todo: write test
            $this->attachmentApi->sendPhotoLink($message);
            Logger::logMessageOut($message);
        } catch (\Throwable $t) {
            Logger::logError($idIn, $t);
        }
    }
}