<?php

namespace OLBot\Category;


use OLBot\Model\DB\Answer;

class PictureResponse extends AbstractCategory
{
    public function generateResponse()
    {
        self::$storageService->response->pics[] = Answer::where(['category' => $this->categoryNumber])->random()->text;
    }
}