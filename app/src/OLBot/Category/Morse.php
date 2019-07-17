<?php


namespace OLBot\Category;


/*
 * uses https://repat.github.io/morsecode-api/
 */

use GuzzleHttp\Client;

class Morse extends AbstractCategory
{
    private $url;
    private $morseRegex = '#^[. -]+$#';

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings = [], $categoryhits = [])
    {
        $this->url = $settings['url'];
        $this->needsSubject = true;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $client = new Client();
        $text = self::$storageService->subjectCandidates[$this->subjectIndex]->text;
        $res = $client->get(
            $this->url.'/'
            .(preg_match_all($this->morseRegex, $text) ? 'decode' : 'encode')
            .'?string='
            .urlencode($text)
        );

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody()->getContents(), true);
            self::$storageService->response->text[] = $result[preg_match_all($this->morseRegex, $text) ? 'plaintext' : 'morsecode'];
        } else {
            throw new \Exception('morsecode api error: '.$res->getStatusCode());
        }
    }
}