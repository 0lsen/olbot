<?php

namespace OLBot\Category;


use OLBot\Model\CategoryHits;
use OLBot\Model\DB\Answer;
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
    const CAT_LATEST = 92;

    /** @var StorageService */
    public static $storageService;

    protected $needsSubject = false;

    protected $requiredCategoryHits;

    public $requirementsMet = true;

    protected $categoryNumber;

    protected $latest;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits)
    {
        $this->categoryNumber = $categoryNumber;
        $this->requiredCategoryHits = $settings['requiredCategoryHits'] ?? [];
        $this->latest = ($settings['allowLatest'] ?? false) && $this->latestCategoryHit($categoryhits);
        $this->requirementsMet = $this->areRequirementsMet($subjectCandidateIndex, $categoryhits);
    }

    /**
     * @param CategoryHits[] $categoryHits
     * @return bool
     */
    private function latestCategoryHit($categoryHits)
    {
        foreach ($categoryHits as $category) {
            if ($category->id == self::CAT_LATEST && $category->hits) {
                return true;
            }
        }
        return false;
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

    protected function getAnswer()
    {
        $answers = Answer::where(['category' => $this->categoryNumber]);
        if (!$answers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        return $this->latest ? $answers->get()->last() : $answers->inRandomOrder()->first();
    }
}