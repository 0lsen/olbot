<?php


namespace OLBot\Category;


use GuzzleHttp\Client;

class Translation extends AbstractCategory
{
    private $langCodeRegex = '#^\w{2}$#';
    private $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
    private $translationSettings;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings = [], $categoryhits = [])
    {
        $this->needsSubject = true;
        $this->translationSettings = $settings['yandexTranslationSettings'];
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $text = $this->getText();

        $lang = null;

        if ($this->isSubjectLanguage($text) ) {
            $text = $this->getText();
        }

        foreach (self::$storageService->subjectCandidates as $index => $candidate) {
            if ($lang == null && $index != $this->subjectIndex && preg_match($this->langCodeRegex, $candidate->text)) {
                $lang = $candidate->text;
            }
        }

        if (is_null($lang)) {
            $lang = $this->findLanguage();
        }

        if (is_null($lang)) {
            $lang = $this->translationSettings['standardLanguage'];
        }

        $client = new Client();

        $res = $client->post($this->url.'?key='.$this->translationSettings['apiKey'].'&lang='.$lang, [
            'form_params' => [
                'text' => $text
            ]
        ]);

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody()->getContents());
            self::$storageService->response->text[] = "`".$result->lang."`\n".$result->text[0];
        } else {
            throw new \Exception('translation api error: '.$res->getStatusCode());
        }
    }

    private function isSubjectLanguage($text)
    {
        if (
            $this->subjectIndex == 0 &&
            (isset($this->translationSettings['languageMap'][$text]) || preg_match($this->langCodeRegex, $text)) &&
            sizeof(self::$storageService->subjectCandidates) > 1
        ) {
            $this->subjectIndex = 1;
            return true;
        } else {
            return false;
        }
    }

    private function findLanguage()
    {
        $text = $this->removeSubjectCandidate();
        preg_match_all('#\w+#', $text, $matches);
        foreach ($matches[0] as $match) {
            if (isset($this->translationSettings['languageMap'][$match])) {
                return $this->translationSettings['languageMap'][$match];
            }
        }
        return null;
    }
}