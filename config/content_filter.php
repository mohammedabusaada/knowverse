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
];