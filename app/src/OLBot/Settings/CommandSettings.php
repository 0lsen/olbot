<?php

namespace OLBot\Settings;


class CommandSettings
{
    public $addJokeCommand;
    public $addFlatteryCommand;
    public $addInsultCommand;

    public $replyToNewEntry;
    public $replyToEntryAlreadyKnown;
    public $customCommands;

    public function __construct(string $addJokeCommand, string $addFlatteryCommand, string $addInsultCommand, string $replyToNewEntry, string $replyToEntryAlreadyKnown, $customCommands)
    {
        $this->addJokeCommand = $addJokeCommand;
        $this->addFlatteryCommand = $addFlatteryCommand;
        $this->addInsultCommand = $addInsultCommand;

        $this->replyToNewEntry = $replyToNewEntry;
        $this->replyToEntryAlreadyKnown = $replyToEntryAlreadyKnown;

        foreach ($customCommands as $index => $command) {
            $this->customCommands[] = new CustomCommandSettings(
                $index,
                $command['type'],
                $command['name'],
                $command['replyToNewEntry'] ?? $this->replyToNewEntry,
                $command['replyToEntryAlreadyKnown'] ?? $this->replyToEntryAlreadyKnown,
                $command['botmasterOnly'] ?? false
            );
        }
    }
}