<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class TextResponse extends AbstractCategory
{
    public function generateResponse()
    {
        self::$storageService->response->text[] = Answer::where(['category' => $this->categoryNumber])->random()->text;
    }
}