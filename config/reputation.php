<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gamification Economy & Reputation Points
    |--------------------------------------------------------------------------
    | These values govern the platform's economy. Adjusting these requires
    | consideration of inflation and user behavior manipulation.
    | Positive values reward constructive actions, negative penalize them.
    */
    'points' => [
        // Content Creation
        'post_created'           => 5,
        'comment_created'        => 2,
        
        // Consensus & Quality
        'authors_pick_received'   => 15,
        'authors_pick_awarded'    => 2,

        // Voting Dynamics (Posts)
        'post_upvoted'           => 5,
        'post_downvoted'         => -2,

        // Voting Dynamics (Comments)
        'comment_upvoted'        => 2,
        'comment_downvoted'      => -1,
    ],
];