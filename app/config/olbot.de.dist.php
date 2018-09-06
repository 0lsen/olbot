<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    [
        'replyToNewEntry' => 'Vielen Dank!',
        'replyToEntryAlreadyKnown' => 'Kenne ich schon.',
        'commands' => [
            'addJoke' => [
                'call' => 'neuerWitz',
            ],
            'addFlattery' => [
                'call' => 'neueSchmeichelei',
            ],
            'addInsult' => [
                'call' => 'neueBeleidigung',
            ],
        ],
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
        'subjectDelimiters' => ':',
    ]
);