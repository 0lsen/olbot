<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class LearningTextResponse extends TextResponse
{
    private $replacements;


    public function __construct($categoryNumber, $subjectCandidateIndex, $settings = [], $categoryhits = [])
    {
        $this->replacements = $settings['replacements'] ?? [];
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $text = self::$storageService->textCopy;
        foreach ($this->replacements as $search => $replace) {
            $text = preg_replace($search, $replace, $text);
        }
        if (!$this->similiarAnswerIsKnown($text)) {
            Answer::create(['category' => $this->categoryNumber, 'text' => $text, 'author' => self::$storageService->user->id]);
        }
        parent::generateResponse();
    }
}