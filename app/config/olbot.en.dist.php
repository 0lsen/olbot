<?php

return [
    'token' => 'asd',
    'botmaster_id' => '123456789',
    'command' => [
        'commands' => [
            'addJoke' => 'addJoke',
            'addInsult' => 'addInsult',
            'addFlattery' => 'addFlattery',
            'addPicture' => 'addPicture'
            /*
             * TODO: maybe custom things would be better this way: (no Eloquent Models though)
             * 'addJoke' => [
             *   'type' => 'text',
             *   'db_table' => 'joke',
             *   'already_exists_message' => 'I already know this ... .',
             *   'botmaster_only' => false,
             *   ...
             * ]
             */
        ],
        'reply_new_entry' => 'Thank you for your contribution.',
        'reply_entry_already_known' => 'I already know this.'
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