<?php

return [
    'token' => 'mytoken',
    'botName' => 'olbot',
    'botmasterId' => '123456789',
    'fallbackErrorResponse' => 'Something went terribly wrong.',
    'cache' => [
        'active' => true,
        'type' => 'tmp',
        'ttl' => 5*24*60*60
    ],
    'command' => [
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'replyToInvalidInput' => 'Invalid Input.',
        'commandList' => [
            [
                'commandType' => 'CategoryAnswer',
                'name' => 'categoryAnswer',
                'category' => 2,
                'contentType' => 'text'
            ],
            [
                'commandType' => 'CategoryAnswer',
                'name' => 'categoryPicture',
                'category' => 1,
                'contentType' => 'pic'
            ],
            [
                'commandType' => 'CategoryAnswer',
                'name' => 'category2Arguments',
                'category' => 2,
                'contentType' => 'text',
                'numberOfArguments' => 2
            ],
        ],
    ],
    'instantResponses' => [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true,
        ],
    ],
    'parser' => [
        'categories' => [
            [
                'categoryNumber' => 1,
                'type' => 'Math',
                'phpythagorasSettings' => [
                    'apiKey' => 'foo',
                    'decimalpoint' => '.',
                    'groupSeparator' => ',',
                    'divisionByZeroResponse' => 'I refuse to divide by zero.'
                ]
            ],
            [
                'categoryNumber' => 2,
                'type' => 'TextResponse',
                'allowLatestQuery' => true,
                'appendAuthor' => true
            ],
            [
                'categoryNumber' => 3,
                'type' => 'PictureResponse',
            ],
            [
                'categoryNumber' => 4,
                'type' => 'TextResponse',
                'requiredCategoryHits' => [
                    [
                        'key' => 4,
                        'value'=> 2
                    ],
                    [
                        'key' => 5,
                        'value' => 1
                    ]
                ],
                'appendAuthor' => true
            ],
            [
                'categoryNumber' => 5,
                'type' => 'LearningTextResponse'
            ],
            [
                'categoryNumber' => 6,
                'type' => 'Weather',
                'openWeatherSettings' => [
                    'apiKey' => 'foo',
                    'fallbackPlace' => 'Berlin',
                    'units' => 'metric',
                    'lang' => 'en'
                ]
            ],
            [
                'categoryNumber' => 7,
                'type' => 'Translation',
                'yandexTranslationSettings' => [
                    'apiKey' => 'foo',
                    'standardLanguage' => 'en'
                ]
            ],
            [
                'categoryNumber' => 8,
                'type' => 'Status'
            ]
        ],
        'stringReplacements' => [
            [
                'key' => 'ö',
                'value'=> 'o'
            ]
        ],
        'quotationMarks' => [
            [
                'key' => '"',
                'value' => '"'
            ],
            [
                'key' => '\'',
                'value' => '\''
            ],
            [
                'key' => '„',
                'value' => '“',
            ]
        ],
        'subjectDelimiters' => [':', 'in '],
    ]
];