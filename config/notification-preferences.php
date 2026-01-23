<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Categories
    |--------------------------------------------------------------------------
    |
    | These are the notification types that the system supports.
    | Branch 2 only defines user preferences and rules.
    | Branch 1 will later trigger notifications using these keys.
    |
    */

    'categories' => [

        'comment' => [
            'label'   => 'New comments on my posts',
            'default' => true,
        ],

        'follow' => [
            'label'   => 'Someone followed me',
            'default' => true,
        ],

        'vote' => [
            'label'   => 'Votes on my content',
            'default' => true,
        ],

        'best_answer' => [
            'label'   => 'My comment was marked as best',
            'default' => true,
        ],

    ],

];
