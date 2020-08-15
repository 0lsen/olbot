<?php

return new \OLBot\Settings(
    # Bot Token
    '123:token',

    # Bot Name
    'oLbot',

    # ID des Bot Master
    '1234566',

    # Fallback Fehlermeldung
    'Ups! Irgendwas ist schiefgelaufen.',

    # Commands
    [
        # Fallback Antworten
        'replyToNewEntry' => 'Dankeschön!',
        'replyToEntryAlreadyKnown' => 'Kenne ich schon.',
        'replyToInvalidInput' => 'Input ist falsch.',

        # Command Liste
        #   class: Klasse in OLBot\Command\
        #   settings: (für class 'CategoryAnswer')
        #     category: Kategorie, aus der Antwort gezogen wird (siehe Kategorien)
        #     type: text (default), pic
        'commands' => [
            'witz' => [
                # Textcommand - ohne Argument wird bekannter Text aus DB geholt, mit Argument neuer eingefügt
                'class' => 'CategoryAnswer',
                'settings' => [
                    'category' => 1
                ]
            ],
            'katzenbild' => [
                # Bildcommand - ohne Argument wird bekanntes Bild (als URL) aus DB geholt, mit Argument (URL) neues eingefügt
                'class' => 'CategoryAnswer',
                'settings' => [
                    'category' => 2,
                    'type' => 'pic'
                ]
            ],
        ],
    ],

    # Sofortantworten
    #   regex: Regex-Trigger für Anfrage
    #   response: Antwort
    #   break: soll Anfrage weiter verarbeitet werden?
    [
        [
            'regex' => '#^Hakuna$#',
            'response' => 'Matata',
            'break' => true,
        ],
    ],

    # Karma (WIP)
    [
        'function' => '',
        'step' => 0.1,
    ],

    # Parser Einstellungen
    [
        # Kategorien, die per Stichwort/wörter getriggert werden können
        'categories' => [
            1 => [
                # Text aus gelernten Texten (alle Settings optional)
                #   appendAuthor: Username anhängen, der den Text via Command eingepflegt hat
                #   allowLatest: Mit Stichwort aus Kategorie 92 darf neuester Text angefordert werden
                #   requiredCategoryHits: Exakt beschreibbar, wieviele Kategorietreffer (auch verschiedener Kategorien) für Trigger notwendig sind
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
                # Bild aus gelernten Bildern
                'class' => 'PictureResponse',
            ],
            3 => [
                # Mathe: gibt String zum Auswerten an die PHPythagoras API weiter
                # Zu übergebender String ist bestimmt durch `quotationMarks` & `subjectDelimiters` (siehe unten)
                'class' => 'Math',
                'settings' => [
                    'phpythagorasSettings' => [
                        'apiKey' => 'myapikey',
                        'decimalpoint' => ',',
                        'groupSeparator' => '.',
                        'divisionByZeroResponse' => 'Ich teile nicht durch Null.'
                    ]
                ]
            ],
            4 => [
                # Text mit Lerneffekt (Bsp. "Deine Mutter..."-Sprüche)
                # Prüft beim Lernen, ob ähnlicher Text (= 80% Worte stimmen überein) schon bekannt ist
                #   replacements:  Regex Ersetzungen, bspw. um Dopplungen zu vermeiden oder gelernte Formulierung zu vereinheitlichen.
                'class' => 'LearningTextResponse',
                'settings' => [
                    'replacements' => [
                        '#^deine#' => 'Deine',
                        '#(m|M)udda#' => 'Mutter'
                    ]
                ]
            ],
            5 => [
                # Wetter an gewissem Ort bestimmen
                # Zu übergebender Ort ist bestimmt durch `quotationMarks` & `subjectDelimiters` (siehe unten), sonst Fallback
                'class' => 'Weather',
                'settings' => [
                    'openWeatherSettings' => [
                        'apiKey' => 'apiKey',
                        'fallbackPlace' => 'London',
                        'units' => 'metric',
                        'lang' => 'de'
                    ]
                ]
            ],
            6 => [
                # Markov-Chain basierte Antwort, generiert aus `resources`
                #   resources: Dateien in `resources` Ordner
                #   cache: active (default false), type ('tmp' oder 'apcu')
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
                # Morse Code Übersetzung über eine einfache API
                # Zu übergebender String ist bestimmt durch `quotationMarks` & `subjectDelimiters` (siehe unten)
                'class' => 'Morse',
                'settings' => [
                    'url' => 'http://www.morsecode-api.de'
                ]
            ],
            8 => [
                # Übersetzung
                # Zielsprache wird aus zweistelligem Sprachkürzel (oder via languageMap), mit Fallback bestimmt
                # und ist ebenso wie zu übersetzender String durch `quotationMarks` & `subjectDelimiters` bestimmt (siehe unten)
                'class' => 'Translation',
                'settings' => [
                    'yandexTranslationSettings' => [
                        'apiKey' => 'apiKey',
                        'standardLanguage' => 'de',
                        'languageMap' => [
                            'englisch' => 'en',
                            'franzoesisch' => 'fr',
                            'deutsch' => 'de'
                        ]
                    ]
                ]
            ],
            9 => [
                # Gruppen anschreiben (nur Botmaster)
                # Zielgruppe und Text sind durch `quotationMarks` & `subjectDelimiters` bestimmt (siehe unten)
                'class' => 'SendTextToGroup',
                'settings' => []
            ],
        ],

        # Umlaute u.ä. werden umgeformt, bevor Stichwortabgleich stattfindet (um zB Groß/Kleinschreibung zu ignorieren)
        'stringReplacements' => [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
        ],

        # Wenn im Freitext Parameter erkannt werden müssen,
        # soll der "beste" über Dinge wie Anführungszeichen, Doppelpunkt oder Stichworte identifiziert werden
        'quotationMarks' => [
            '"' => '"',
            '\'' => '\'',
            '„' => '“',
        ],
        'subjectDelimiters' => [':', 'in '],
    ]
);