<?php

namespace OLBot\Settings;


class ParserSettings
{
    public $categories;
    public $stringReplacements;
    public $quotationMarks;
    public $subjectDelimiter;

    public function __construct($categories, $stringReplacements, $quotationMarks, $subjectDelimiter)
    {
        $this->categories = $categories;
        $this->stringReplacements = $stringReplacements;
        $this->quotationMarks = $quotationMarks;
        $this->subjectDelimiter = $subjectDelimiter;
    }
}