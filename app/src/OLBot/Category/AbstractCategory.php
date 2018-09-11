<?php

namespace OLBot\Category;


use OLBot\Service\StorageService;

abstract class AbstractCategory
{
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