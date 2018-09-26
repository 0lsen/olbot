<?php

namespace OLBot\Category;


use OLBot\Model\DB\AllowedUser;
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
        if ($this->appendAuthor && $answer->author) {
            $author = AllowedUser::find($answer->author);
            if ($author) {
                $answer->text .= "\n    _-" . $author->name . "_";
            }
        }
        self::$storageService->response->text[] = $answer->text;;
    }
}