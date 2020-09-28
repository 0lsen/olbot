<?php

namespace OLBot\Category;


class PictureResponse extends AbstractCategory
{
    /**
     * @throws \Exception
     */
    public function generateResponse() : void
    {
        $answer = $this->getAnswer();
        self::$storageService->response->pics[] = $answer->text;
    }
}