<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blocked Words List
    |--------------------------------------------------------------------------
    |
    | Any content containing these strings (case-insensitive) will be 
    | blocked before publication.
    |
    */

    'blocked_words' => [
        // Toxicity
        'idiot', 'moron', 'dumbass', 'fucker', 'bitched', 
        
        // Hate Speech (Add the actual slurs here)
        'racist_slur_1', 'homophobic_slur_1', 'nazi', 'hitler',
        
        // Scams
        'whatsapp +', 'telegram @', 'make money fast', 'free crypto',
        
        // Inappropriate
        'porn', 'brazzers', 'sex video', 'xxx',
    ],
];