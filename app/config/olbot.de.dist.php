<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    [
        'replyToNewEntry' => 'Vielen Dank!',
        'replyToEntryAlreadyKnown' => 'Kenne ich schon.',
        'commands' => [
            'AddJoke' => [
                'call' => 'neuerWitz',
            ],
            'AddFlattery' => [
                'call' => 'neueSchmeichelei',
            ],
            'AddInsult' => [
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
        'categories' => [
            1 => 'Math',
            2 => 'TextResponse',
            3 => 'PictureResponse',
        ],
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