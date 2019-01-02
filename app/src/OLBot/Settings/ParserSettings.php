<?php

namespace OLBot\Settings;


class ParserSettings
{
    public $categories;
    public $stringReplacements;
    public $math;
    public $translation;
    public $quotationMarks;
    public $subjectDelimiter;

    public function __construct($categories, $stringReplacements, $translation, $quotationMarks, $subjectDelimiter)
    {
        $this->categories = $categories;
        $this->stringReplacements = $stringReplacements;
        $this->translation = new TranslationSettings($translation['fallbackLanguage'], $translation['typicalLanguageEnding']);
        $this->quotationMarks = $quotationMarks;
        $this->subjectDelimiter = $subjectDelimiter;
    }
}