<?php

namespace OLBot\Settings;


class ParserSettings
{
    public $math;
    public $translation;
    public $quotationMarks;
    public $subjectDelimiter;

    public function __construct($math, $translation, $quotationMarks, $subjectDelimiter)
    {
        $this->math = new MathSettings($math['decimalPoint'], $math['divisionByZeroResponse']);
        $this->translation = new TranslationSettings($translation['fallbackLanguage'], $translation['typicalLanguageEnding']);
        $this->quotationMarks = $quotationMarks;
        $this->subjectDelimiter = $subjectDelimiter;
    }
}