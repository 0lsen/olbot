<?php

namespace OLBot\Model;


class SubjectCandidate
{
    const QUOTATION = 0;
    const DELIMITER = 1;

    public $type;
    public $mark;
    public $text;

    function __construct($type, $mark, $text)
    {
        $this->type = $type;
        $this->mark = $mark;
        $this->text = $text;
    }
}