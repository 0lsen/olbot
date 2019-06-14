<?php

namespace OLBot\Category;


use OLBot\Model\DB\AllowedUser;

class TextResponse extends AbstractCategory
{
    private $appendAuthor;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings = [], $categoryhits = [])
    {
        $this->appendAuthor = $settings['appendAuthor'] ?? false;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    public function generateResponse()
    {
        $answer = $this->getAnswer();
        if ($this->appendAuthor && $answer->author) {
            $author = AllowedUser::find($answer->author);
            if ($author) {
                $answer->text .= "\n    _-" . $author->name . "_";
            }
        }
        self::$storageService->response->text[] = $answer->text;
    }
}