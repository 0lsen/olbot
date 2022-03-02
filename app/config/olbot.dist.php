<?php

return [
    'token' => '123:token',
    'botName' => 'oLbot',
    'botmasterId' => '1234566',
    'fallbackErrorResponse' => 'Oops, something went wrong.',
    'cache' => [
        'active' => true,
        # current apcu or tmp
        'type' => 'apcu',
        # time to live in seconds
        'ttl' => 3*24*60*60
    ],
    'command' => [
        # fallback replies
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'replyToInvalidInput' => 'Invalid Input.',
        'commandList' => [
            [
                # without an argument a category answer will be picked from DB
                # with an argument it will be added to DB if not already known
                'commandType' => 'CategoryAnswer',
                # command, e.g. /joke
                'name' => 'joke',
                # respective category
                'category' => 1,
                # type text: category content is text
                'contentType' => 'text'
            ],
            [
                'commandType' => 'CategoryAnswer',
                'name' => 'catpic',
                'category' => 2,
                # type pic: category content is pic, argument should be a URL
                'contentType' => 'pic'
            ],
        ],
    ],
    # Regex triggers that result in a respective response
    'instantResponses' => [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            # should message processing be halted?
            'break' => true,
        ],
    ],
    'parser' => [
        # list of categories triggered by category keyword(s)
        'categories' => [
            [
                'categoryNumber' => 1,
                # simple text response from respective category
                'type' => 'TextResponse',
                # (optional) append author name of chosen response
                'appendAuthor' => true,
                # (optional) with keyword from category 92 the most recent registered response may be requested
                'allowLatestQuery' => true,
                # (optional) allow to request response from specific author
                'allowAuthorQuery' => true,
                # (optional) specify how many keywords (value) of each category (key) are necessary to trigger
                'requiredCategoryHits' => [
                    [
                        'key' => 1,
                        'value' => 2
                    ]
                ]
            ],
            [
                'categoryNumber' => 2,
                # simple picture response from respective category
                'type' => 'PictureResponse'
            ],
            [
                'categoryNumber' => 3,
                # forward a string to the PHPythagoras API for evaluation
                # requires query string as argument
                'type' => 'Math',
                'phpythagorasSettings' => [
                    'apiKey' => 'myapikey',
                    'decimalpoint' => ',',
                    'groupSeparator' => '.',
                    'divisionByZeroResponse' => 'I refuse to divide by zero.'
                ]
            ],
            [
                'categoryNumber' => 4,
                # text with learning property (e.g. "yo moma..." jokes)
                # checks if a similiar text (= 80% of words in common) is already known
                'type' => 'LearningTextResponse',
                # regex replacements to avoid double entries and help uniformity
                'replacements' => [
                    [
                        'key' => '#^yo#',
                        'value' => 'Yo'
                    ],
                    [
                        'key' => '#(m|M)other#',
                        'value' => 'moma'
                    ]

                ]
            ],
            [
                'categoryNumber' => 5,
                # weather for a certain place
                # uses query string as argument, otherwise fallbackPlace
                'type' => 'Weather',
                'openWeatherSettings' => [
                    'apiKey' => 'apiKey',
                    'fallbackPlace' => 'London',
                    'units' => 'metric',
                    'language' => 'en'
                ]
            ],
            [
                'categoryNumber' => 6,
                # Markov-chain based response
                'type' => 'Markov',
                'markovSettings' => [
                    # files in resources folder as basis
                    'resources' => [
                        'test.txt'
                    ],
                    'sentenceThreshold' => 4,
                    'wordThreshold' => 50,
                    'elementLength' => 2,
                    'endOfSentence' => '.?!'
                ]
            ],
            [
                'categoryNumber' => 7,
                # morse code translation via simple API
                # requires query string as argument
                'type' => 'Morse',
            ],
            [
                'categoryNumber' => 8,
                # translation
                # target language is recognised by a two character query string (or via languageMap), with fallback
                # requires query string as argument
                'type' => 'Translation',
                'yandexTranslationSettings' => [
                    'apiKey' => 'apiKey',
                    'standardLanguage' => 'en',
                    'languageMap' => [
                        [
                            'key' => 'english',
                            'value' => 'en'
                        ],
                        [
                            'key' => 'french',
                            'value' => 'fr'
                        ],
                        [
                            'key' => 'german',
                            'value' => 'de'
                        ]
                    ]
                ]
            ],
            [
                'categoryNumber' => 9,
                # write to group (bot master only)
                # requires two query strings as arguments (group name and message)
                'type' => 'SendTextToGroup',
            ],
            [
                'categoryNumber' => 10,
                # status answer with "#catX#" and "#noX#" (with integer X) placeholders for category descriptions and number of known answers
                'type' => 'Status'
            ]
        ],

        # replacements to perform before checking keywords, mainly to lowercase special characters
        'stringReplacements' => [
            [
                'key' => 'ä',
                'value' => 'ae'
            ],
            [
                'key' => 'ö',
                'value' => 'oe'
            ],
            [
                'key' => 'ü',
                'value' => 'ue'
            ],
            [
                'key' => 'Ä',
                'value' => 'Ae'
            ],
            [
                'key' => 'Ö',
                'value' => 'Oe'
            ],
            [
                'key' => 'Ü',
                'value' => 'Ue'
            ],
        ],

        # for argument/query string detection in text
        # the "best" candidates should be found via things like quotation marks, colon or keywords
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
                'value' => '“'
            ],
        ],
        'subjectDelimiters' => [':\\s+', '\\bin\\s+'],
        'authorHints' => ['\\bfrom (\w+)\\b'],
    ]
];