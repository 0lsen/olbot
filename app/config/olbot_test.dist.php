<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    'Something went terribly wrong.',
    [
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'commands' => [
            [
                'class' => 'AddJoke',
                'call' => 'addJoke',
            ],
            [
                'class' => 'AddFlattery',
                'call' => 'addFlattery',
            ],
            [
                'class' => 'AddInsult',
                'call' => 'addInsult',
            ],
            [
                'class' => 'AddCategoryAnswer',
                'call' => 'addCategoryAnswer',
                'settings' => [
                    'category' => 1,
                ]
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