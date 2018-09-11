<?php

namespace OLBot;


use OLBot\Model\DB\AllowedUser;

class Util
{
    static function replacePlaceholders(array $placeholders, string &$text)
    {
        foreach ($placeholders as $search => $replace) {
            $text = str_replace('#'.$search.'#', $replace, $text);
        }
    }

    static function isUsersBirthdayToday(AllowedUser $user) {
        // TODO: check if it's users birthday, return age else return null
        return null;
    }
}