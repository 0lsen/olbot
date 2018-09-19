<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class PictureResponse extends AbstractCategory
{
    public function generateResponse()
    {
        $answers = Answer::where(['category' => $this->categoryNumber]);
        if (!$answers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        self::$storageService->response->pics[] = $answers->inRandomOrder()->first()->text;
    }
}