<?php

namespace OLBot\Model;

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

    public function randomSuccessor($simple = true) : string
    {
        $result = null;
        $index = rand(1, $this->sum);
        foreach ($this->successors as $successor => $count) {
            if ($count >= $index) {
                return $this->start || $simple ? $successor : substr($successor, strrpos($successor, ' ')+1);
            }
            $index -= $count;
        }
    }
}