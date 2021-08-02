<?php

namespace OLBot\Category;


use Illuminate\Database\Eloquent\Builder;
use OLBot\Model\CategoryHits;
use OLBot\Model\DB\AllowedUser;
use OLBot\Model\DB\Answer;
use OLBot\Service\CacheService;
use OLBot\Service\StorageService;
use OLBot\Util;
use OLBotSettings\Model\CategorySettings;

abstract class AbstractCategory implements CategoryInterface
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

    /** @var CategoryHits[] */
    protected $categoryHits;

    protected $requirementsMet = true;

    protected $categoryNumber;

    protected $latest;

    protected $author;

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
        $this->latest = ($settings->getAllowLatestQuery() ?? false) && $this->latestCategoryHit($categoryhits);
        $this->author = $settings->getAllowAuthorQuery() ? self::$storageService->authorHint : null;
        $this->requirementsMet = $this->areRequirementsMet($subjectCandidateIndex, $categoryhits);
    }

    /**
     * @param CategoryHits[] $categoryHits
     * @return bool
     */
    private function latestCategoryHit(array $categoryHits)
    {
        foreach ($categoryHits as $category) {
            if ($category->getId() == self::CAT_LATEST && $category->getHits()) {
                return true;
            }
        }
        return false;
    }

    private function areRequirementsMet($subjectCandidateIndex, $categoryHits)
    {
        $requirementsMet = $this->subjectRequirements($subjectCandidateIndex) && $this->categoryRequirements($categoryHits);
        if ($requirementsMet) {
            $this->categoryHits = $categoryHits;
        }
        return $requirementsMet;
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
                if ($hits->getId() == $category) {
                    if (sizeof($hits->getHits()) >= $requiredHits) {
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

    protected function similiarAnswerIsKnown(string $text1)
    {
        $text1 = Util::replace($text1, self::$storageService->settings->getParser()->getStringReplacements());
        if ($this->textContainsOnlyKeywords($text1)) {
            return true;
        }
        $this->getAnswers();
        foreach ($this->answers->get() as $answer) {
            $text2 = Util::replace($answer->text, self::$storageService->settings->getParser()->getStringReplacements());
            if (Util::textIsSimilar($text1, $text2)) {
                return true;
            }
        }
        return false;
    }

    private function textContainsOnlyKeywords(string $text) : bool
    {
        foreach (Util::getWords($text) as $word) {
            $found = false;
            foreach ($this->categoryHits as $categoryHit) {
                if (in_array($word, $categoryHit->getHits())) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return false;
            }
        }
        return true;
    }

    private function getAnswers()
    {
        if (is_null($this->answers)) {
            if ($this->author) {
                $author = AllowedUser::where(['name' => $this->author])->get()->first();
                if ($author) {
                    $this->answers = Answer::where(['category' => $this->categoryNumber, 'author' => $author->id]);
                }
            }
            if (!$this->answers) {
                $this->answers = Answer::where(['category' => $this->categoryNumber]);
            }
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

    public function requirementsMet(): bool
    {
        return $this->requirementsMet;
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