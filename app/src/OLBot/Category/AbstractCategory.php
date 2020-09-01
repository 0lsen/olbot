<?php

namespace OLBot\Category;


use Illuminate\Database\Eloquent\Builder;
use OLBot\Model\CategoryHits;
use OLBot\Model\DB\Answer;
use OLBot\Service\CacheService;
use OLBot\Service\StorageService;
use OLBot\Util;
use OLBotSettings\Model\CategorySettings;

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
    protected static $storageService;

    /** @var CacheService */
    protected static $cacheService;

    protected $needsSubject = false;

    protected $subjectIndex;

    protected $requiredCategoryHits = [];

    public $requirementsMet = true;

    protected $categoryNumber;

    protected $latest;

    /** @var Builder */
    private $answers = null;

    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, CategorySettings $settings, $categoryhits = [])
    {
        $this->categoryNumber = $categoryNumber;
        if ($settings->getRequiredCategoryHits()) {
            foreach ($settings->getRequiredCategoryHits() as $tuple) {
                $this->requiredCategoryHits[$tuple->getKey()] = $tuple->getValue();
            }
        }
        $this->latest = ($settings->getAllowLatest() ?? false) && $this->latestCategoryHit($categoryhits);
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
        $requirementMet = !($this->needsSubject && !is_null($subjectCandidateIndex) && !isset(self::$storageService->subjectCandidates[$subjectCandidateIndex]));
        if ($this->needsSubject && $requirementMet) {
            $this->subjectIndex = $subjectCandidateIndex;
        }
        return $requirementMet;
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

    protected function similiarAnswerIsKnown($text1) {
        $this->replaceText($text1);
        $this->getAnswers();
        foreach ($this->answers->get() as $answer) {
            $text2 = $answer->text;
            $this->replaceText($text2);
            if (Util::textIsSimilar($text1, $text2)) {
                return true;
            }
        }
        return false;
    }

    private function replaceText(&$text) {
        if (self::$storageService->settings->getParser()->getStringReplacements()) {
            foreach (self::$storageService->settings->getParser()->getStringReplacements() as $tuple) {
                $text = str_replace($tuple->getKey(), $tuple->getValue(), $text);
            }
        }
    }

    private function getAnswers() {
        if (is_null($this->answers)) {
            $this->answers = Answer::where(['category' => $this->categoryNumber]);
        }
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     * @throws \Exception
     */
    protected function getAnswer()
    {
        $this->getAnswers();
        if (!$this->answers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        return $this->latest ? $this->answers->get()->last() : $this->answers->inRandomOrder()->first();
    }

    protected function getText()
    {
        return self::$storageService->subjectCandidates[$this->subjectIndex]->text;
    }

    protected function removeSubjectCandidate()
    {
        return preg_replace('#\{'.($this->subjectIndex+1).'}.+{/'.($this->subjectIndex+1).'}#', '', self::$storageService->textCopy);
    }

    /**
     * @param StorageService $storageService
     */
    public static function setStorageService(StorageService $storageService): void
    {
        self::$storageService = $storageService;
    }

    /**
     * @param CacheService $cacheService
     */
    public static function setCacheService(CacheService $cacheService): void
    {
        self::$cacheService = $cacheService;
    }
}