<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blocked Words List
    |--------------------------------------------------------------------------
    |
    | Any content containing these strings (case-insensitive) will be
    | blocked before publication. Maintain this list regularly.
    |
    */

    'blocked_words' => [
        // Toxicity & Profanity
        'fuck', 'asshole', 'shit', 'bitch', 'idiot', 'moron', 'dumbass',

        // Hate Speech & Extremism
        'nazi', 'hitler', 'racist_slur_here',

        // Spam & Scams
        'whatsapp +', 'telegram @', 'make money fast', 'free crypto',

        // NSFW / Inappropriate
        'porn', 'brazzers', 'sex video', 'xxx',
    ],

    /*
    | Maximum number of external links allowed in a single submission before it is
    | treated as spam. Trusted users (see reputation.privileges) bypass this limit.
    */
    'max_links' => 3,
];
