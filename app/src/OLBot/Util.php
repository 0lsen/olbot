<?php

namespace OLBot;


use OLBot\Model\DB\AllowedUser;
use OLBotSettings\Model\StringTuple;

class Util
{
    private static $wordLength = 3;

    static function getWords(string $text) : array
    {
        preg_match_all('#\w{'.self::$wordLength.',}#', strtolower($text), $words);
        return array_unique($words[0]);
    }

    /**
     * @param string $text
     * @param StringTuple[] $replacements
     * @return string|string[]
     */
    static function replace(string $text, $replacements) : string
    {
        if ($replacements) {
            foreach ($replacements as $tuple) {
                $find = $tuple->getKey();
                $replace = $tuple->getValue();
                $text = str_replace($find, $replace, $text);
            }
        }
        return $text;
    }

    static function replacePlaceholders(array $placeholders, string &$text)
    {
        foreach ($placeholders as $search => $replace) {
            $text = str_replace('#'.$search.'#', $replace, $text);
        }
    }

    static function isUsersBirthdayToday(AllowedUser $user)
    {
        // TODO: check if it's users birthday, return age else return null
        return null;
    }

    // TODO: via settings
    private static $textSimilarityThreshold = 0.8;

    static function textIsSimilar($text1, $text2)
    {
        $text1 = strtolower($text1);
        $text2 = strtolower($text2);
        preg_match_all('#\w{3,}#', $text1, $t1Tokens);
        preg_match_all('#\w{3,}#', $text2, $t2Tokens);

        if (sizeof($t2Tokens[0]) == 0 || sizeof($t1Tokens[0])/sizeof($t2Tokens[0]) < self::$textSimilarityThreshold) {
            return false;
        }

        $matches = 0;
        foreach ($t1Tokens[0] as $token) {
            if (in_array($token, $t2Tokens[0])) $matches++;
        }

        return $matches/sizeof($t1Tokens[0]) >= self::$textSimilarityThreshold;
    }
}