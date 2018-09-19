<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class TextResponse extends AbstractCategory
{
    public function generateResponse()
    {
        $ansers = Answer::where(['category' => $this->categoryNumber]);
        if (!$ansers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        self::$storageService->response->text[] = $ansers->inRandomOrder()->first()->text;
    }
}