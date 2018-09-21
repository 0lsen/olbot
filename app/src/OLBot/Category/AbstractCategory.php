<?php

namespace OLBot\Category;


use OLBot\Model\CategoryHits;
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

    protected $requiredCategoryHits;

    public $requirementsMet = true;

    protected $categoryNumber;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits)
    {
        $this->categoryNumber = $categoryNumber;
        $this->requiredCategoryHits = $settings['requiredCategoryHits'] ?? [];
        $this->requirementsMet = $this->areRequirementsMet($subjectCandidateIndex, $categoryhits);
    }

    public function generateResponse() {}

    private function areRequirementsMet($subjectCandidateIndex, $categoryHits)
    {
        return $this->subjectRequirements($subjectCandidateIndex)
            && $this->categoryRequirements($categoryHits);
    }

    private function subjectRequirements($subjectCandidateIndex)
    {
        return !($this->needsSubject && !isset(self::$storageService->subjectCandidates[$subjectCandidateIndex]));
    }

    private function categoryRequirements($categoryHits)
    {
        $requirementsMet = true;
        foreach ($this->requiredCategoryHits as $category => $requiredHits) {
            $hit = false;
            /** @var CategoryHits $hits */
            foreach ($categoryHits as $hits) {
                if ($hits->id == $category) {
                    if ($hits->hits >= $requiredHits) {
                        $hit = true;
                    }
                    break;
                }
            }
            if (!$hit) {
                $requirementsMet = false;
                break;
            }
        }
        return $requirementsMet;
    }
}