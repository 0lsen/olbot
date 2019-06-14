<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;
use OLBot\Util;

abstract class AbstractCommand
{
    static $standardReplyToNewEntry;
    static $standardReplyToEntryAlreadyKnown;
    static $standardReplyToInvalidInput;

    protected $replyToNewEntry;
    protected $replyToEntryAlreadyKnown;
    protected $replyToInvalidInput;
    protected $checkSimilarity;

    /** @var StorageService */
    public static $storageService;

    protected $numberOfArguments;

    public function __construct($settings = [])
    {
        $this->replyToNewEntry = $settings['replyToNewEntry'] ?? self::$standardReplyToNewEntry;
        $this->replyToEntryAlreadyKnown = $settings['replyToEntryAlreadyKnown'] ?? self::$standardReplyToEntryAlreadyKnown;
        $this->replyToInvalidInput = $settings['replyToInvalidInput'] ?? self::$standardReplyToInvalidInput;
        $this->checkSimilarity = $settings['checkSimilarity'] ?? false;
        $this->numberOfArguments = $settings['numberOfArguments'] ?? 0;
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
        foreach (self::$storageService->settings->parser->stringReplacements as $find => $replace) {
            $text = str_replace($find, $replace, $text);
        }
    }

    protected function addNew($eloquentModel, $text, $author, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::create', array_merge(['text' => $text, 'author' => $author], $conditions));
    }
}