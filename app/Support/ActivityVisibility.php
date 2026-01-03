<?php

namespace App\Support;

class ActivityVisibility
{
    /**
     * Action → visibility level
     */
    public static function map(): array
    {
        return [

            // Public content creation
            'post_created'            => 'public',
            'comment_created'         => 'public',
            'best_answer_selected'    => 'public',

            // Votes (private by default)
            'vote_up'                 => 'private',
            'vote_down'               => 'private',
            'vote_removed'            => 'private',

            // Reputation (private but visible to owner)
            'reputation_changed'      => 'private',

            // Auth
            'login'                   => 'private',
            'logout'                  => 'private',
        ];
    }

    /**
     * Resolve visibility for an action
     */
    public static function for(string $action): string
    {
        return self::map()[$action] ?? 'private';
    }
}
