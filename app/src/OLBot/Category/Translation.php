<?php


namespace OLBot\Category;


use OLBot\Adapter\Yandex as YandexAdapter;
use OLBotSettings\Model\Translation as TranslationSettings;

class Translation extends AbstractCategory
{
    private $langCodeRegex = '#^\w{2}$#';
    private $translationSettings;
    private $languageMap = [];

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, TranslationSettings $settings, $categoryhits = [])
    {
        $this->needsSubject = true;
        $this->translationSettings = $settings->getYandexTranslationSettings();
        if ($this->translationSettings->getLanguageMap()) {
            foreach ($this->translationSettings->getLanguageMap() as $tuple) {
                $this->languageMap[$tuple->getKey()] = $tuple->getValue();
            }
        }
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    /**
     * @throws \Exception
     */
    public function generateResponse() : void
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
            $lang = $this->translationSettings->getStandardLanguage();
        }

        $adapter = new YandexAdapter();
        $response = $adapter->send($text, $lang, $this->translationSettings->getApiKey());

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents());
            self::$storageService->response->text[] = "`".$result->lang."`\n".$result->text[0];
        } else {
            throw new \Exception('translation api error: '.$response->getStatusCode());
        }
    }

    private function isSubjectLanguage($text)
    {
        if (
            $this->subjectIndex == 0 &&
            (isset($this->languageMap[$text]) || preg_match($this->langCodeRegex, $text)) &&
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
            if (isset($this->languageMap[$match])) {
                return $this->languageMap[$match];
            }
        }
        return null;
    }
}