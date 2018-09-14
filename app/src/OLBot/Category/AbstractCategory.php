<?php

namespace OLBot\Category;


use OLBot\Service\StorageService;

abstract class AbstractCategory
{
    // 70+ for cron related categories (for now)
    const CAT_BIRTHDAY_REMINDER = 70;
    const CAT_BIRTHDAY_GREETING = 71;
    const CAT_ERROR_REPORT = 72;

    // 90+ for misc. categories (for now)
    const CAT_FALLBACK = 90;
    const CAT_ERROR = 91;

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