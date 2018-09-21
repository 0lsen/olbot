<?php

namespace OLBot\Category;


class Math extends AbstractCategory
{
    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits)
    {
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryHits);
        $this->requirementsMet = sizeof(self::$storageService->response->math) > 0;
    }

    public function generateResponse()
    {
        foreach (self::$storageService->response->math as $math) {
            self::$storageService->response->text[] = $math;
        }
    }
}