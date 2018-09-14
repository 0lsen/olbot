<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    'Something went terribly wrong.',
    [
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'commands' => [
            'AddJoke' => [
                'call' => 'addJoke',
            ],
            'AddFlattery' => [
                'call' => 'addFlattery',
            ],
            'AddInsult' => [
                'call' => 'addInsult',
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
            'decimalPoint' => '.',
            'divisionByZeroResponse' => 'Division by Zero is evil.',
        ],
        'translation' => [
            'fallbackLanguage' => 'english',
            'typicalLanguageEnding' => 'ese'
        ],
        'quotationMarks' => '"\'',
        'subjectDelimiters' => ':',
    ]
);