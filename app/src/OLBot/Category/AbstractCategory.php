<?php

namespace OLBot\Category;


use OLBot\Service\StorageService;

abstract class AbstractCategory
{
    const CAT_FALLBACK = 99;
    const CAT_BIRTHDAY_GREETING = 98;
    const CAT_BIRTHDAY_REMINDER = 97;

    /** @var StorageService */
    public static $storageService;

    protected $needsSubject = false;

    public $requirementsMet = true;

    protected $categoryNumber;

    public function __construct($categoryNumber, $subjectCandidateIndex)
    {
        $this->categoryNumber = $categoryNumber;
        $this->requirementsMet = !($this->needsSubject && !isset(self::$storageService->subjectCandidates[$subjectCandidateIndex]));
    }

    public function generateResponse() {}
}