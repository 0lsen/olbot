<?php

return new \OLBot\Settings(
    'asd',
    '123456789',
    [
        'addJokeCommand' => 'addJoke',
        'addFlatteryCommand' => 'addFlattery',
        'addInsultCommand' => 'addInsult',
        'replyToNewEntry' => 'Thank you for your contribution.',
        'replyToEntryAlreadyKnown' => 'I already know this.',
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
        'math' => [
            'decimalPoint' => '.',
            'divisionByZeroResponse' => 'Division by Zero is evil.',
        ],
        'translation' => [
            'fallbackLanguage' => 'english',
            'typicalLanguageEnding' => 'ese'
        ],
        'quotationMarks' => '"\'',
    ]
);