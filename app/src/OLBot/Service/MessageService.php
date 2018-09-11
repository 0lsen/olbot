<?php

namespace OLBot\Service;


use OLBot\Logger;
use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\ParseMode;
use Swagger\Client\Telegram\SendMessageBody;
use Swagger\Client\Telegram\SendPhotoLinkBody;

class MessageService
{
    private $api;
    private $token;

    function __construct($token)
    {
        $this->api = new MessagesApi();
        $this->token = $token;
    }

    function sendMessage($text, $idOut, $idIn)
    {
        $message = new SendMessageBody();
        $message->setChatId($idOut);
        $message->setReplyToMessageId($idIn);
        $message->setText($text);
        $message->setParseMode(ParseMode::MARKDOWN);
        try {
            $this->api->sendMessage($this->token, $message);
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
            $this->api->sendMessage($this->token, $message);
            Logger::logMessageOut($message);
        } catch (\Throwable $t) {
            Logger::logError($idIn, $t);
        }
    }
}