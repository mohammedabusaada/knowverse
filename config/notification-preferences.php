<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Categories
    |--------------------------------------------------------------------------
    | These match the NotificationType Enum values.
    */
    'categories' => [
        'post_commented' => [
            'label'   => 'New comments on my posts',
            'default' => true,
        ],
        'comment_replied' => [
            'label'   => 'Replies to my comments',
            'default' => true,
        ],
        'user_followed' => [
            'label'   => 'New followers',
            'default' => true,
        ],
        'post_upvoted' => [
            'label'   => 'Upvotes on my posts',
            'default' => true,
        ],
        'authors_pick_received' => [
            'label'   => 'My response is selected as Author\'s Pick',
            'default' => true,
        ],
        'report_resolved' => [
            'label'   => 'Updates on reports I submitted',
            'default' => true,
        ],
        'content_removed' => [
            'label'   => 'Notifications about my content being hidden',
            'default' => true,
        ],
    ],
];