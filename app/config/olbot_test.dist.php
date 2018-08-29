<?php

return [
    'token' => 'asd',
    'botmaster_id' => '123456789',
    'command' => [
        'commands' => [
            'addJoke' => 'addJoke',
            'addInsult' => 'addInsult',
            'addFlattery' => 'addFlattery',
            'addPicture' => 'addPicture',
        ],
        'reply_new_entry' => 'Thank you for your contribution.',
        'reply_entry_already_known' => 'I already know this.'
    ],
    'instant_responses' => [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true
        ],
    ],
    'letter_conversion' => [
        'from' => [

        ],
        'to' => [

        ]
    ],
    'math' => [
        'decimal_point' => '.',
        'dbz_message' => 'Division by Zero is evil.'
    ],
    'translation' => [
        'fallback_language' => 'english',
        'typical_language_ending' => 'ese'
    ],
    'karma' => [
        'step' => 0.1
    ]
];