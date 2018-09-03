<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    [
        'addJokeCommand' => 'neuerWitz',
        'addFlatteryCommand' => 'neueSchmeichelei',
        'addInsultCommand' => 'neueBeleidigung',
        'replyToNewEntry' => 'Vielen Dank!',
        'replyToEntryAlreadyKnown' => 'Kenne ich schon.',
    ],
    [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true,
        ],
    ],
    [
        'function' => '',
        'step' => 0.1,
    ],
    [
        'math' => [
            'decimalPoint' => ',',
            'divisionByZeroResponse' => 'Durch Null teilen ist böse.',
        ],
        'translation' => [
            'fallbackLanguage' => 'english',
            'typicalLanguageEnding' => 'isch'
        ],
        'quotationMarks' => '"\'',
    ]
);