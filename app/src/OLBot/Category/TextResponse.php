<?php

namespace OLBot\Category;


use OLBot\Model\DB\AllowedUser;
use OLBotSettings\Model\TextResponse as TextResponseSettings;

class TextResponse extends AbstractCategory
{
    private $appendAuthor;

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, TextResponseSettings $settings, $categoryhits = [])
    {
        $this->appendAuthor = $settings->getAppendAuthor() ?? false;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    /**
     * @throws \Exception
     */
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