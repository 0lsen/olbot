<?php


namespace OLBot\Adapter;


use GuzzleHttp\Client;

class Yandex
{
    private $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';

    /**
     * @param string $text
     * @param string $language
     * @param string $apiKey
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(string $text, string $language, string $apiKey) {
        $client = new Client();
        return $client->post($this->url.'?key='.$apiKey.'&lang='.$language, [
            'form_params' => [
                'text' => $text
            ]
        ]);
    }

}