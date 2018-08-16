<?php

namespace OLBot\Service;


use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\ParseMode;
use Swagger\Client\Telegram\SendMessageBody;

class MessageService
{
    private $api;
    private $token;

    function __construct($token)
    {
        $this->api = new MessagesApi();
        $this->token = $token;
    }

    function sendMessage($text, $id)
    {
        $message =  new SendMessageBody();
        $message->setChatId($id);
        $message->setText($text);
        $message->setParseMode(ParseMode::MARKDOWN);
        $this->api->sendMessage($this->token, $message);
    }
}