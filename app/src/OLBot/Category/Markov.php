<?php

namespace OLBot\Category;


class Markov extends AbstractCategory
{
    private $markovSettings;
    private $endOfSentence;
    private $elementLength;
    private $sentenceStart = '###START###';

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits)
    {
        $this->markovSettings = $settings['markovSettings'];
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
    }

    public function generateResponse()
    {
        $this->endOfSentence = $this->markovSettings['endOfSentence'] ?? '.!?';
        $this->elementLength = $this->markovSettings['elementLength'] ?? 1;

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
        $wordThreshold = $this->markovSettings['wordThreshold'] ?? 30;
        $sentenceThreshold = $this->markovSettings['sentenceThreshold'] ?? 4;

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

    private function lastWord(string $string) {
        preg_match('#[^ ]+$#', $string, $match);
        return $match[0];
    }
}

class MarkovChainElement {
    private $successors = [];
    private $sum = 1;
    private $start;

    public function __construct(string $word, $start = false)
    {
        $this->successors[$word] = 1;
        $this->start = $start;
    }

    public function add(string $word) {
        if (isset($this->successors[$word])) {
            $this->successors[$word]++;
        } else {
            $this->successors[$word] = 1;
        }
        $this->sum++;
    }

    public function randomSuccessor($simple = true)
    {
        $result = null;
        $index = rand(1, $this->sum);
        foreach ($this->successors as $successor => $count) {
            if ($count >= $index) {
                return $this->start || $simple ? $successor : substr($successor, strpos($successor, ' ')+1);
            }
            $index -= $count;
        }
    }
}