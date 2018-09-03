<?php

namespace OLBot\Settings;


class CustomCommandSettings
{
    const TEXT = 0;
    const PICTURE = 1;
    public $command;
    public $type;
    public $name;
    public $replyToNewEntry;
    public $replyToEntryAlreadyKnown;
    public $botmasterOnly;

    public function __construct(string $command, int $type, string $name, string $replyToNewEntry, string $replyToEntryAlreadyKnown, bool $botmasterOnly = false)
    {
        $this->command = $command;
        $this->type = $type;
        $this->name = $name;
        $this->replyToNewEntry = $replyToNewEntry;
        $this->replyToEntryAlreadyKnown = $replyToEntryAlreadyKnown;
        $this->botmasterOnly = $botmasterOnly;
    }
}