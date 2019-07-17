<?php

namespace OLBot\Category;


use Illuminate\Database\Eloquent\Builder;
use OLBot\Model\CategoryHits;
use OLBot\Model\DB\Answer;
use OLBot\Service\StorageService;
use OLBot\Util;

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

    protected $subjectIndex;

    protected $requiredCategoryHits;

    public $requirementsMet = true;

    protected $categoryNumber;

    protected $latest;

    /** @var Builder */
    private $answers = null;

    public function __construct($categoryNumber, $subjectCandidateIndex, $settings = [], $categoryhits = [])
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
        foreach (self::$storageService->settings->parser->stringReplacements as $find => $replace) {
            $text = str_replace($find, $replace, $text);
        }
    }

    private function getAnswers() {
        if (is_null($this->answers)) {
            $this->answers = Answer::where(['category' => $this->categoryNumber]);
        }
    }

    protected function getAnswer()
    {
        $this->getAnswers();
        if (!$this->answers->count()) throw new \Exception('no answer found for category '.$this->categoryNumber);
        return $this->latest ? $this->answers->get()->last() : $this->answers->inRandomOrder()->first();
    }
}