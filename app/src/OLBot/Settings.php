<?php

namespace OLBot;


use OLBot\Command\AbstractCommand;
use OLBot\Settings\CommandSettings;
use OLBot\Settings\InstantResponseSettings;
use OLBot\Settings\KarmaSettings;
use OLBot\Settings\ParserSettings;

class Settings
{
    public $token;
    public $botName;
    public $botmasterId;
    public $fallbackErrorResponse;
    /** @var CommandSettings[] */
    public $commands;
    /** @var InstantResponseSettings[] */
    public $instantResponses;
    public $karma;
    public $parser;

    public function __construct($token, $botName, $botmasterId, $fallbackErrorResponse, $commands, $instantResponses, $karma, $parser)
    {
        $this->token = $token;
        $this->botName = $botName;
        $this->botmasterId = $botmasterId;
        $this->fallbackErrorResponse = $fallbackErrorResponse;

        AbstractCommand::$standardReplyToNewEntry = $commands['replyToNewEntry'];
        AbstractCommand::$standardReplyToEntryAlreadyKnown = $commands['replyToEntryAlreadyKnown'];
        AbstractCommand::$standardReplyToInvalidInput = $commands['replyToInvalidInput'];

        foreach ($commands['commands'] as $index => $command) {
            $this->commands[$index] = new CommandSettings(
                $command['class'],
                $command['settings'] ?? []
            );
        }

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
            $parser['categories'],
            $parser['stringReplacements'],
            $parser['math'],
            $parser['translation'],
            $parser['quotationMarks'],
            $parser['subjectDelimiters']
        );
    }
}