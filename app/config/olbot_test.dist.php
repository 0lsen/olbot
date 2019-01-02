<?php

return new \OLBot\Settings(
    'asd',
    'olbot',
    '123456789',
    'Something went terribly wrong.',
    [
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'replyToInvalidInput' => 'Invalid Input.',
        'commands' => [
            'addFlattery' => [
                'class' => 'AddFlattery',
            ],
            'addInsult' => [
                'class' => 'AddInsult',
            ],
            'addCategoryAnswer' => [
                'class' => 'AddCategoryAnswer',
                'settings' => [
                    'category' => 1
                ]
            ],
            'addCategoryPicture' => [
                'class' => 'AddCategoryAnswer',
                'settings' => [
                    'category' => 1,
                    'type' => 'pic'
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
            1 => [
                'class' => 'Math',
                'settings' => [
                    'phpythagorasSettings' => [
                        'apiKey' => 'foo',
                        'decimalpoint' => '.',
                        'groupSeparator' => ',',
                        'divisionByZeroResponse' => 'I refuse to divide by zero.'
                    ]
                ]
            ],
            2 => [
                'class' => 'TextResponse',
                'settings' => [
                    'allowLatest' => true
                ]
            ],
            3 => [
                'class' => 'PictureResponse',
            ],
            4 => [
                'class' => 'TextResponse',
                'settings' => [
                    'requiredCategoryHits' => [
                        4 => 2,
                        5 => 1
                    ],
                    'appendAuthor' => true
                ]
            ],
            6 => [
                'class' => 'Weather',
                'settings' => [
                    'openWeatherSettings' => [
                        'apiKey' => 'foo',
                        'fallbackPlace' => 'Berlin',
                        'units' => 'metric',
                        'lang' => 'en'
                    ]
                ]
            ]
        ],
        'stringReplacements' => [
            'ö' => 'o'
        ],
        'translation' => [
            'fallbackLanguage' => 'english',
            'typicalLanguageEnding' => 'ese'
        ],
        'quotationMarks' => [
            '"' => '"',
            '\'' => '\'',
            '„' => '“',
        ],
        'subjectDelimiters' => [':', 'in '],
    ]
);