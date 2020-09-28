<?php


namespace OLBot\Category;


use OLBot\Adapter\Morse as MorseAdapter;
use OLBotSettings\Model\Morse as MorseSettings;

class Morse extends AbstractCategory
{
    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, MorseSettings $settings, $categoryhits = [])
    {
        $this->needsSubject = true;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    /**
     * @throws \Exception
     */
    public function generateResponse() : void
    {
        $text = self::$storageService->subjectCandidates[$this->subjectIndex]->text;
        $adapter = new MorseAdapter();
        $response = $adapter->send($text);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            self::$storageService->response->text[] = $result[preg_match_all($adapter->getRegex(), $text) ? 'plaintext' : 'morsecode'];
        } else {
            throw new \Exception('morsecode api error: '.$response->getStatusCode());
        }
    }
}