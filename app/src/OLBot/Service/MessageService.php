<?php

namespace OLBot\Service;


use Guzzle\Http\Client;

class MessageService
{
    private $uri = 'https://api.telegram.org/bot';

    function __construct($token)
    {
        $this->uri .= $token;
    }

    function sendMessage($text, $id)
    {
        $this->send('sendMessage', [
            'chat_id' => $id,
            'text'=> $text
        ]);
    }

    private function send($route, $payload)
    {
        $client = new Client();
        $res = $client->post($this->uri.'/'.$route, ['Content-Type' => 'application/json'], $payload);
    }
}