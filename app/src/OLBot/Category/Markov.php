<?php

namespace OLBot\Category;


use OLBot\Model\MarkovChainElement;
use OLBotSettings\Model\Markov as MarkovSettings;

class Markov extends AbstractCategory
{
    private $markovSettings;
    private $endOfSentence;
    private $elementLength;
    private $sentenceStart = '###START###';

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, MarkovSettings $settings, $categoryHits = [])
    {
        $this->markovSettings = $settings->getMarkovSettings();
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
    }

    /**
     * @throws \Exception
     */
    public function generateResponse()
    {
        $this->endOfSentence = $this->markovSettings->getEndOfSentence() ?? '.!?';
        $this->elementLength = $this->markovSettings->getElementLength() ?? 1;

        $cacheKey = 'olbot_markov_'.$this->categoryNumber;
        $elements = self::$cacheService->fetch($cacheKey);

        if (!$elements) {
            $elements = $this->buildChainElements();
            if (sizeof($elements) < 2) {
                throw new \Exception('Markov could not acquire knowledge.');
            }
            self::$cacheService->store($cacheKey, $elements);
        }

        return $this->generateText($elements);
    }

    /**
     * @return MarkovChainElement[]
     */
    private function buildChainElements()
    {
        $elements = [];
        foreach ($this->markovSettings->getResources() as $resource) {
            $data = file_get_contents(PROJECT_ROOT . '/app/resources/' . $resource);
            if ($data) {
                preg_match_all('#(?<=^|['.$this->endOfSentence.'] )([^ ]+)( (?1)){'.($this->elementLength-1).'}#', $data, $firstWords);
                foreach ($firstWords[0] as $index => $word) {
                    if ($index) {
                        $elements[$this->sentenceStart]->add($word);
                    } else {
                        $elements[$this->sentenceStart] = new MarkovChainElement($word, true);
                    }
                }

                $predecessor = [];
                $actual = [];
                preg_match_all('#[^ ]+#', $data, $words);
                foreach ($words[0] as $index => $word) {
                    $actual[] = $word;
                    if (sizeof($actual) <= $this->elementLength) {
                        $predecessor[] = $word;
                        continue;
                    }
                    $endOfSentence  = preg_match('#['.$this->endOfSentence.']$#', end($predecessor));
                    $lengthInsufficient = sizeof($actual) <= $this->elementLength;
                    if (!$endOfSentence && !$lengthInsufficient) {
                        array_splice($actual, 0, 1);
                        $key = implode(' ', $predecessor);
                        $value = implode(' ', $actual);
                        if (isset($elements[$key])) {
                            $elements[$key]->add($value);
                        } else {
                            $elements[$key] = new MarkovChainElement($value);
                        }
                        $predecessor[] = $word;
                        if (sizeof($predecessor) > $this->elementLength) {
                            array_splice($predecessor, 0, 1);
                        }
                    } elseif($endOfSentence) {
                        $actual = [$word];
                        $predecessor = [$word];
                    } else {
                        $actual = [];
                        $predecessor = [];
                    }
                }
            }
        }

        return $elements;
    }

    /**
     * @param MarkovChainElement[] $elements
     */
    private function generateText($elements)
    {
        $wordThreshold = $this->markovSettings->getWordThreshold() ?? 30;
        $sentenceThreshold = $this->markovSettings->getSentenceThreshold() ?? 4;

        $simple = $this->elementLength == 1;
        $words = [];
        $lastKey = '';
        $sentences = 1;
        $endOfSentence = true;
        while (!($sentences > $sentenceThreshold) && (!(sizeof($words) > $wordThreshold) || !$endOfSentence)) {
            $words[] = $elements[$endOfSentence ? $this->sentenceStart : $lastKey]->randomSuccessor($simple);
            $lastKey = ($endOfSentence || $simple ? '' : $this->lastWord($words[sizeof($words)-2]).' ') . end($words);
            $endOfSentence = preg_match('#['.$this->endOfSentence.']$#', end($words));
            if ($endOfSentence) $sentences++;
        }

        self::$storageService->response->text[] = implode(' ', $words);
    }

    private function lastWord(string $string) : string {
        preg_match('#[^ ]+$#', $string, $match);
        return $match[0];
    }
}