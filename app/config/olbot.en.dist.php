<?php

return new \OLBot\Settings(
    # bot token
    'asd',

    # ID of the bot master
    '123456789',

    # fallback error response
    'Something went terribly wrong',

    # command settings
    [
        # fallback replies to commands (assuming standard commands are used to add some new piece of knowledge)
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',

        # list of commands
        # key is the command's class name
        # 'call' specifies which string '/call' triggers the command
        # 'settings' is an optional array to specify a command's replies or add parameters needed in a custom command class' constructor
        #            this might also be possible as a value: \OLBot\Model\DB\Answer::where(['category' => #no])->random()->text
        'commands' => [
            'AddJoke' => [
                'call' => 'addJoke',
                'settings' => [
                    'replyToNewEntry' => 'lol'
                ]
            ],
            'AddFlattery' => [
                'call' => 'addFlattery',
            ],
            'AddInsult' => [
                'call' => 'addInsult',
            ],
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
            1 => 'Math',
            2 => 'TextResponse',
            3 => 'PictureResponse',
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