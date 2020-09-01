<?php


namespace OLBot\Adapter;


use GuzzleHttp\Client;

class Morse
{
    private $url = 'http://www.morsecode-api.de';
    private $regex = '#^[. -]+$#';

    /**
     * @param string $text
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(string $text) {
        $client = new Client();
        return $client->get(
            $this->url.'/'
            .(preg_match_all($this->regex, $text) ? 'decode' : 'encode')
            .'?string='
            .urlencode($text)
        );
    }
}