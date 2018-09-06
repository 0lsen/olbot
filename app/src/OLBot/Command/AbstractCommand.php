<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;

abstract class AbstractCommand
{
    static $standardReplyToNewEntry;
    static $standardReplyToEntryAlreadyKnown;

    protected $replyToNewEntry;
    protected $replyToEntryAlreadyKnown;

    protected $storageService;

    protected $numberOfArguments;

    public function __construct(StorageService $storageService, $settings = [])
    {
        $this->storageService = $storageService;
        $this->replyToNewEntry = $settings['replyToNewEntry'] ?? self::$standardReplyToNewEntry;
        $this->replyToEntryAlreadyKnown = $settings['replyToEntryAlreadyKnown'] ?? self::$standardReplyToEntryAlreadyKnown;
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
        return $this->storageService->textCopy != '' && sizeof(explode(' ', $this->storageService->textCopy)) >= $this->numberOfArguments;
    }

    protected function tryToAddNewText($eloquentModel, $conditions = []) {
        $alreadyKnown = $this->isTextAlreadyKnown($eloquentModel, $this->storageService->textCopy, $conditions);
        $this->storageService->response['main'][] =
            $alreadyKnown
                ? $this->replyToEntryAlreadyKnown
                : $this->replyToNewEntry;
        if (!$alreadyKnown) {
            $this->addNew($eloquentModel, $this->storageService->textCopy, $this->storageService->user->id, $conditions);
        }
    }

    protected function isTextAlreadyKnown($eloquentModel, $text, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::where', array_merge(['text' => $text], $conditions))->count();
    }

    protected function addNew($eloquentModel, $text, $author, $conditions)
    {
        return call_user_func('\OLBot\Model\DB\\' . $eloquentModel . '::create', array_merge(['text' => $text, 'author' => $author], $conditions));
    }
}