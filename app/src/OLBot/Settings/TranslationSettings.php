<?php

namespace OLBot\Settings;


class TranslationSettings
{
    public $fallbackLanguage;
    public $typicalLanguageEnding;
    
    public function __construct($fallbackLanguage, $typicalLanguageEnding)
    {
        $this->fallbackLanguage = $fallbackLanguage;
        $this->typicalLanguageEnding = $typicalLanguageEnding;
    }
}