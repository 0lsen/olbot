<?php

namespace OLBot\Category;


class Markov extends AbstractCategory
{
    private $markovSettings;
    private $endOfSentence;
    private $sentenceStart = '###START###';

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits)
    {
        $this->markovSettings = $settings['markovSettings'];
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
    }

    public function generateResponse()
    {
        $this->endOfSentence = $this->markovSettings['endOfSentence'] ?? '.!?';
        $ignoreCache = $this->markovSettings['ignoreCache'] ?? false;
        if (!$ignoreCache) {
            $cacheKey = $this->markovSettings['cacheKey'] ?? 'markovCache';
            $elements = apcu_fetch($cacheKey);
        } else {
            $elements = null;
        }
        if (!$elements) {
            $elements = $this->buildChainElements();
            if (sizeof($elements) < 2) {
                throw new \Exception('Markov could not acquire knowledge.');
            }
            if (!$ignoreCache) {
                apcu_store($cacheKey, $elements, 24*60*60);
            }
        }

        return $this->generateText($elements);
    }

    private function buildChainElements()
    {
        $elements = [];
        foreach ($this->markovSettings['resources'] as $resource) {
            $data = file_get_contents(PROJECT_ROOT . '/app/resources/' . $resource);
            if ($data) {
                preg_match_all('#(?<=^|['.$this->endOfSentence.'] )[^ ]+#', $data, $firstWords);
                foreach ($firstWords[0] as $index => $word) {
                    if ($index) {
                        $elements[$this->sentenceStart]->add($word);
                    } else {
                        $elements[$this->sentenceStart] = new MarkovChainElement($word);
                    }
                }

                $predecessor = null;
                preg_match_all('#[^ ]+#', $data, $words);
                foreach ($words[0] as $index => $word) {
                    $endOfSentence  = !$index || preg_match('#['.$this->endOfSentence.']$#', $predecessor);
                    if (!$endOfSentence) {
                        if (isset($elements[$predecessor])) {
                            $elements[$predecessor]->add($word);
                        } else {
                            $elements[$predecessor] = new MarkovChainElement($word);
                        }
                    }
                    $predecessor = $word;
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
        $wordThreshold = $this->markovSettings['wordThreshold'] ?? 30;
        $sentenceThreshold = $this->markovSettings['sentenceThreshold'] ?? 4;

        $words = [];
        $sentences = 1;
        $endOfSentence = true;
        while (!($sentences > $sentenceThreshold) && (!(sizeof($words) > $wordThreshold) || !$endOfSentence)) {
            $words[] = $elements[$endOfSentence ? $this->sentenceStart : end($words)]->randomSuccessor();
            $endOfSentence = preg_match('#['.$this->endOfSentence.']$#', end($words));
            if ($endOfSentence) $sentences++;
        }

        self::$storageService->response->text[] = implode(' ', $words);
    }
}

class MarkovChainElement {
    private $successors = [];
    private $sum = 1;

    public function __construct(string $word)
    {
        $this->successors[$word] = 1;
    }

    public function add(string $word) {
        if (isset($this->successors[$word])) {
            $this->successors[$word]++;
        } else {
            $this->successors[$word] = 1;
        }
        $this->sum++;
    }

    public function randomSuccessor()
    {
        $result = null;
        $index = rand(1, $this->sum);
        foreach ($this->successors as $successor => $count) {
            if ($count >= $index) {
                return $successor;
            }
            $index -= $count;
        }
    }
}