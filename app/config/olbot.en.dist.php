<?php

return new \OLBot\Settings(
    # bot token
    'asd',

    # bot name to react to group mentions
    'olbot',

    # ID of the bot master
    '123456789',

    # fallback error response
    'Something went terribly wrong',

    # command settings
    [
        # fallback replies to commands (assuming standard commands are used to add some new piece of knowledge)
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
        'replyToInvalidInput' => 'Invalid Input.',

        # list of commands
        # 'class' specifies what class to use
        # 'call' specifies which string '/call' triggers the command
        # 'settings'
        #       'replyToNewEntry'/'replyToEntryAlreadyKnown'/'replyToInvalidInput': custom responses
        #       'numberOfArguments': for custom commands: expected number of arguments
        #       'category': for AddCategoryAnswer: what category will the provided answer be added to
        'commands' => [
            'addFlattery' => [
                'class' => 'AddFlattery',
            ],
            'addInsult' => [
                'class' => 'AddInsult',
            ],
            'addCustomCategoryAnswer' => [
                'class' => 'AddCategoryAnswer',
                'settings' => [
                    'category' => 1,
                    'type' => 'text' // expecting 'text' or 'pic' so far, 'text' is default
                ]
            ]
        ],
    ],

    # instant responses as a list
    # 'regex' specifies the responses trigger
    # 'response' is the response (duh) added to your answer
    # 'break' decides an instant return is triggered
    [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true,
        ],
    ],

    # karma settings
    # TODO: implement functions to calculate karma (string or actual function)
    [
        'function' => '',
        'step' => 0.1,
    ],

    # parser settings
    [
        # list of categories, numbers corresponding to those in the keywords db
        'categories' => [
            1 => [
                # 'class' specifying the category class (duh)
                # 'settings'
                #       'requiredCategoryHits' specifying how many hits on specific categories are required
                #       'appendAuthor' for class 'TextResponse': sign line with the answer's author, if available
                #       'allowLatest' allows to request the newest answer by a hit on category 92
                'class' => 'Math',
                'settings' => [
                    'requiredCategoryHits' => [
                        1 => 1,
                    ],
                    'appendAuthor' => true,
                    'allowLatest' => true
                ]
            ],
            2 => [
                'class' => 'TextResponse',
            ],
            3 => [
                'class' => 'PictureResponse',
            ],
        ],

        # replacements to perform before checking keywords, for now mainly to lowercase special characters (like umlauts)
        'stringReplacements' => [
            'Ä' => 'Ae'
        ],

        # math settings
        'math' => [
            'decimalPoint' => '.',
            'divisionByZeroResponse' => 'Division by Zero is evil.',
        ],

        # TODO: implement this crap
        'translation' => [
            'fallbackLanguage' => 'english',
            'typicalLanguageEnding' => 'ese'
        ],

        # stuff to find possible subjects, like 'foo "i am a possible subject"' and 'foo: i am too'
        'quotationMarks' => '"\'',
        'subjectDelimiters' => ':',
    ]
);