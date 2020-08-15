<?php

return new \OLBot\Settings(
    # bot token
    '123:token',

    # bot name
    'oLbot',

    # ID of the bot master
    '1234566',

    # fallback error response
    'Oops, something went wrong.',

    # commands
    [
        # fallback replies
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'replyToInvalidInput' => 'Invalid Input.',

        # list of commands
        #   class: OLBot\Command\ class
        #   settings: (for class 'CategoryAnswer')
        #     category: referenced category (see categories)
        #     type: text(default), pic
        'commands' => [
            'joke' => [
                # text command - without an argument a known text is fetched from DB, with an argument it will be added
                'class' => 'CategoryAnswer',
                'settings' => [
                    'category' => 1
                ]
            ],
            'catpic' => [
                # pic command - without an argument a known pic (URL) is fetched from DB, with an argument (URL) it will be added
               'class' => 'CategoryAnswer',
               'settings' => [
                   'category' => 2,
                   'type' => 'pic'
               ]
            ],
        ],
    ],

    # instant responses
    #   regex: regex trigger 
    #   response: what it says
    #   break: continue processing request?
    [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true,
        ],
    ],

    # karma (WIP)
    [
        'function' => '',
        'step' => 0.1,
    ],

    # parser settings
    [
        # list of categories triggered by keyword(s)
        'categories' => [
            1 => [
                # text from taught text (all settings optional)
                #   appendAuthor: append user name that provided the chosen text via command
                #   allowLatest: with keyword from category 92 the newest text may be requested
                #   requiredCategoryHits: describes in detail which category hits are necessary to trigger
                'class' => 'TextResponse',
                'settings' => [
                    'appendAuthor' => true,
                    'allowLatest' => true,
                    'requiredCategoryHits' => [
                        1 => 2,
                    ],
                ]
            ],
            2 => [
                # pic from taught pics
                'class' => 'PictureResponse',
            ],
            3 => [
                # math: forwards a string to the PHPythagoras API for evaluation
                # query string is defined by `quotationMarks` & `subjectDelimiters` (see below)
                'class' => 'Math',
                'settings' => [
                    'phpythagorasSettings' => [
                        'apiKey' => 'myapikey',
                        'decimalpoint' => ',',
                        'groupSeparator' => '.',
                        'divisionByZeroResponse' => 'I refuse to divide by zero.'
                    ]
                ]
            ],
            4 => [
                # text with learning property (e.g. "yo moma..." jokes)
                # checks if a similiar text (= 80% of words in common) is already known
                #   replacements: regex replacements to avoid double entries and help uniformity
                'class' => 'LearningTextResponse',
                'settings' => [
                    'replacements' => [
                        '#^yo#' => 'Yo',
                        '#(m|M)other#' => 'moma'
                    ]
                ]
            ],
            5 => [
                # weather for a certain place
                # query string is defined by `quotationMarks` & `subjectDelimiters` (see below), otherwise fallback
                'class' => 'Weather',
                'settings' => [
                    'openWeatherSettings' => [
                        'apiKey' => 'apiKey',
                        'fallbackPlace' => 'London',
                        'units' => 'metric',
                        'lang' => 'en'
                    ]
                ]
            ],
            6 => [
                # Markov-chain based response, generated from `resources`
                #   resources: files in resources folder
                #   cache: active (default false), type ('tmp' or 'apcu')
                'class' => 'Markov',
                'settings' => [
                    'markovSettings' => [
                        'resources' => [
                            'test.txt'
                        ],
                        'cache' => [
                            'active' => true,
                            'type' => 'apcu'
                        ],
                        'sentenceThreshold' => 4,
                        'wordThreshold' => 50,
                        'elementLength' => 2
                    ],
                ]
            ],
            7 => [
                # morse code translation via simple API
                # query string is defined by `quotationMarks` & `subjectDelimiters` (see below)
                'class' => 'Morse',
                'settings' => [
                    'url' => 'http://www.morsecode-api.de'
                ]
            ],
            8 => [
                # translation
                # target language is recognised by a two character string (or via languageMap), with fallback
                # and is like query string defined by `quotationMarks` & `subjectDelimiters` (see below)
                'class' => 'Translation',
                'settings' => [
                    'yandexTranslationSettings' => [
                        'apiKey' => 'apiKey',
                        'standardLanguage' => 'en',
                        'languageMap' => [
                            'english' => 'en',
                            'french' => 'fr',
                            'german' => 'de'
                        ]
                    ]
                ]
            ],
            9 => [
                # write to group (bot master only)
                # target group and text are defined by `quotationMarks` & `subjectDelimiters` (see below)
                'class' => 'SendTextToGroup',
                'settings' => []
            ],
        ],

        # replacements to perform before checking keywords, mainly to lowercase special characters
        'stringReplacements' => [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
        ],

        # for argument detection in text
        # the "best" candidates should be found via things like quotation marks, colon or keywords
        'quotationMarks' => [
            '"' => '"',
            '\'' => '\'',
            '„' => '“',
        ],
        'subjectDelimiters' => [':', 'in '],
    ]
);