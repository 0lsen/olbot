<?php

return [
    'token' => 'asd',
    'botmaster_id' => '123456789',
    'commands' => [
        'commands' => [
            'addJoke' => 'neuerWitz',
            'addInsult' => 'neueBeleidigung',
            'addFlattery' => 'neueSchmeichelei',
            'addPicture' => 'neuesBild'
        ],
        'reply_new_entry' => 'Vielen Dank!',
        'reply_entry_already_known' => 'Kenne ich schon.'
    ],
    'letter_conversion' => [
        'from' => [
            'ä', 'ö', 'ü', 'ß'
        ],
        'to' => [
            'ae', 'oe', 'ue', 'ss'
        ]
    ],
    'math' => [
        'decimal_point' => ',',
        'dbz_message' => 'Durch Null teilen ist böse.'
    ],
    'translation' => [
        'fallback_language' => 'english',
        'typical_language_ending' => 'isch'
    ],
    'karma' => [
        'step' => 0.1
    ]
];