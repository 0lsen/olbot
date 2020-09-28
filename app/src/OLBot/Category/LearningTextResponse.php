<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;
use OLBotSettings\Model\LearningTextResponse as LearningTextResponseSettings;

class LearningTextResponse extends TextResponse
{
    private $replacements = [];

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, LearningTextResponseSettings $settings, $categoryhits = [])
    {
        if ($settings->getReplacements()) {
            foreach ($settings->getReplacements() as $tuple) {
                $this->replacements[$tuple->getKey()] = $tuple->getValue();
            }
        }
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse() : void
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