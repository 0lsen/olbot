<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class TextResponse extends AbstractCategory
{
    private $appendAuthor;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits)
    {
        $this->appendAuthor = $settings['appendAuthor'] ?? false;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $answers = Answer::where(['category' => $this->categoryNumber]);
        if (!$answers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        $answer = $answers->inRandomOrder()->first();
        $text = $this->appendAuthor && $answer->author ? $answer->text . '\n    _-' . $answer->author . '_' : $answer->text;
        self::$storageService->response->text[] = $text;
    }
}