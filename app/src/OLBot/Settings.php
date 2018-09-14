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
    public $botmasterId;
    public $fallbackErrorResponse;
    /** @var CommandSettings[] */
    public $commands;
    /** @var InstantResponseSettings[] */
    public $instantResponses;
    public $karma;
    public $parser;

    public function __construct($token, $botmasterId, $fallbackErrorResponse, $commands, $instantResponses, $karma, $parser)
    {
        $this->token = $token;
        $this->botmasterId = $botmasterId;
        $this->fallbackErrorResponse = $fallbackErrorResponse;

        AbstractCommand::$standardReplyToNewEntry = $commands['replyToNewEntry'];
        AbstractCommand::$standardReplyToEntryAlreadyKnown = $commands['replyToEntryAlreadyKnown'];
        foreach ($commands['commands'] as $name => $command) {
            $this->commands[] = new CommandSettings(
                $command['call'],
                $name,
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
            $parser['math'],
            $parser['translation'],
            $parser['quotationMarks'],
            $parser['subjectDelimiters']
        );
    }
}