<?php

namespace OLBot\Category;


class PictureResponse extends AbstractCategory
{
    public function generateResponse()
    {
        $answer = $this->getAnswer();
        self::$storageService->response->pics[] = $answer->text;
    }
}