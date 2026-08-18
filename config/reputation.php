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

        // Moderation Outcomes (routed through the append-only ledger).
        // Penalty is negative (applied to a violating author); reward is positive
        // (granted to the reporter whose flag was upheld).
        'moderation_penalty'     => -10,
        'report_reward'          => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reputation-Gated Participation Privileges
    |--------------------------------------------------------------------------
    | Minimum cumulative reputation required to unlock each capability. These are
    | PARTICIPATION privileges over the user's own activity — they never grant
    | administrative authority over others (that remains RBAC, assigned manually
    | by an administrator). Thresholds align with the academic-standing tiers.
    */
    'privileges' => [
        'downvote' => 50,    // Junior Researcher: may cast downvotes
        'trusted'  => 100,   // Active Contributor: bypass anti-spam friction
        'pin_post' => 250,   // Associate Researcher: pin own discussions
    ],
];