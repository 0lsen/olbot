<?php

namespace OLBot;


use OLBot\Settings\CommandSettings;
use OLBot\Settings\InstantResponseSettings;
use OLBot\Settings\KarmaSettings;
use OLBot\Settings\ParserSettings;

class Settings
{
    public $token;
    public $botmasterId;
    public $commands;
    /** @var InstantResponseSettings[] */
    public $instantResponses;
    public $karma;
    public $parser;

    public function __construct($token, $botmasterId, $commands, $instantResponses, $karma, $parser)
    {
        $this->token = $token;
        $this->botmasterId = $botmasterId;

        $this->commands = new CommandSettings(
            $commands['addJokeCommand'],
            $commands['addFlatteryCommand'],
            $commands['addInsultCommand'],
            $commands['replyToNewEntry'],
            $commands['replyToEntryAlreadyKnown'],
            $commands['customCommands'] ?? []
        );

        foreach ($instantResponses as $instantResponse) {
            $this->instantResponses[] = new InstantResponseSettings(
                $instantResponse['regex'],
                $instantResponse['response'],
                $instantResponse['break'] ?? false
            );
        }

        $this->karma = new KarmaSettings(
            $karma['step'],
            $karma['function']
        );

        $this->parser = new ParserSettings(
            $parser['math'],
            $parser['translation'],
            $parser['quotationMarks']
        );
    }
}