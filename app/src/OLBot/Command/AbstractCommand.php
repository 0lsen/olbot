<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;
use OLBot\Util;
use OLBotSettings\Model\CommandItemSettings;

abstract class AbstractCommand
{
    private static $standardReplyToNewEntry;
    private static $standardReplyToEntryAlreadyKnown;
    private static $standardReplyToInvalidInput;

    protected $replyToNewEntry;
    protected $replyToEntryAlreadyKnown;
    protected $replyToInvalidInput;
    protected $checkSimilarity;

    /** @var StorageService */
    protected static $storageService;

    protected $numberOfArguments;

    public function __construct(CommandItemSettings $settings)
    {
        $this->replyToNewEntry = $settings->getReplyToNewEntry() ?? self::$standardReplyToNewEntry;
        $this->replyToEntryAlreadyKnown = $settings->getReplyToEntryAlreadyKnown() ?? self::$standardReplyToEntryAlreadyKnown;
        $this->replyToInvalidInput = $settings->getReplyToInvalidInput() ?? self::$standardReplyToInvalidInput;
        $this->checkSimilarity = $settings->getCheckSimilarity() ?? false;
        $this->numberOfArguments = $settings->getNumberOfArguments() ?? 0;
    }

    /**
     * @throws \Exception
     */
    public function doStuff() {
        if (!$this->hasEnoughParameters()) {
            throw new \Exception('not enough parameters.');
        }
    }

    protected function hasEnoughParameters() {
        return self::$storageService->textCopy != '' && $this->numberOfParameters() >= $this->numberOfArguments;
    }

    protected function numberOfParameters() {
        return sizeof(explode(' ', self::$storageService->textCopy));
    }

    protected function tryToAddNewText($eloquentModel, $conditions = []) {
        $alreadyKnown = $this->checkSimilarity
            ? $this->isSimilarTextAlreadyKnown($eloquentModel, $conditions)
            : $this->isTextAlreadyKnown($eloquentModel, self::$storageService->textCopy, $conditions);
        self::$storageService->response->text[] =
            $alreadyKnown
                ? $this->replyToEntryAlreadyKnown
                : $this->replyToNewEntry;
        if (!$alreadyKnown) {
            $this->addNew($eloquentModel, self::$storageService->textCopy, self::$storageService->user->id, $conditions);
        }
    }

    private function isTextAlreadyKnown($eloquentModel, $text, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::where', array_merge(['text' => $text], $conditions))->count();
    }

    private function isSimilarTextAlreadyKnown($eloquentModel, $conditions) {
        $entries = call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::where', $conditions);
        $text1 = self::$storageService->textCopy;
        $this->replaceText($text1);
        foreach ($entries as $entry) {
            $text2 = $entry->text;
            $this->replaceText($text2);
            if (Util::textIsSimilar($text1, $text2)) {
                return true;
            }
        }
        return false;
    }

    private function replaceText(&$text) {
        if (self::$storageService->settings->getParser()->getStringReplacements()) {
            foreach (self::$storageService->settings->getParser()->getStringReplacements() as $find => $replace) {
                $text = str_replace($find, $replace, $text);
            }
        }
    }

    protected function addNew($eloquentModel, $text, $author, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::create', array_merge(['text' => $text, 'author' => $author], $conditions));
    }

    /**
     * @param mixed $standardReplyToNewEntry
     */
    public static function setStandardReplyToNewEntry($standardReplyToNewEntry): void
    {
        self::$standardReplyToNewEntry = $standardReplyToNewEntry;
    }

    /**
     * @param mixed $standardReplyToEntryAlreadyKnown
     */
    public static function setStandardReplyToEntryAlreadyKnown($standardReplyToEntryAlreadyKnown): void
    {
        self::$standardReplyToEntryAlreadyKnown = $standardReplyToEntryAlreadyKnown;
    }

    /**
     * @param mixed $standardReplyToInvalidInput
     */
    public static function setStandardReplyToInvalidInput($standardReplyToInvalidInput): void
    {
        self::$standardReplyToInvalidInput = $standardReplyToInvalidInput;
    }

    /**
     * @param StorageService $storageService
     */
    public static function setStorageService(StorageService $storageService): void
    {
        self::$storageService = $storageService;
    }
}