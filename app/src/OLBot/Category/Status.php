<?php

namespace OLBot\Category;

use OLBot\Model\DB\Answer;
use OLBot\Model\DB\Category;

class Status extends AbstractCategory
{
    /**
     * @throws \Exception
     */
    public function generateResponse() : void
    {
        $answer = $this->getAnswer();

        $text = preg_replace_callback(
            '/#(\w+)(\d+)#/',
            function($match) {
                switch ($match[1]) {
                    case 'cat':
                        return $this->getCategoryDescription($match[2]);
                    case 'no':
                        return $this->getCategoryAnwersNumber($match[2]);
                    default:
                        return $match[0];
                }
            },
            $answer->text
        );
        self::$storageService->response->text[] = $text;
    }

    private function getCategoryDescription(int $no)
    {
        $cat = Category::where(['id' => $no]);
        return $cat->get()->first()->description;
    }

    private function getCategoryAnwersNumber(int $no)
    {
        return Answer::where(['category' => $no])->get()->count();
    }
}