<?php

namespace App\Support;

final class NotificationType
{
    // ------------------------------------------------------------------
    // Post & Comment
    // ------------------------------------------------------------------
    public const POST_COMMENTED = 'post_commented';
    public const COMMENT_REPLIED = 'comment_replied';
    public const BEST_ANSWER_SELECTED = 'best_answer_selected';

    // ------------------------------------------------------------------
    // Votes
    // ------------------------------------------------------------------
    public const POST_UPVOTED = 'post_upvoted';
    public const POST_DOWNVOTED = 'post_downvoted';
    public const COMMENT_UPVOTED = 'comment_upvoted';
    public const COMMENT_DOWNVOTED = 'comment_downvoted';

    // ------------------------------------------------------------------
    // Social
    // ------------------------------------------------------------------
    public const USER_FOLLOWED = 'user_followed';

    // ------------------------------------------------------------------
    // Reputation
    // ------------------------------------------------------------------
    public const REPUTATION_CHANGED = 'reputation_changed';

    // ------------------------------------------------------------------
    // System
    // ------------------------------------------------------------------
    public const SYSTEM = 'system';

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * All notification types.
     */
    public static function all(): array
    {
        return [
            self::POST_COMMENTED,
            self::COMMENT_REPLIED,
            self::BEST_ANSWER_SELECTED,
            self::POST_UPVOTED,
            self::POST_DOWNVOTED,
            self::COMMENT_UPVOTED,
            self::COMMENT_DOWNVOTED,
            self::USER_FOLLOWED,
            self::REPUTATION_CHANGED,
            self::SYSTEM,
        ];
    }

    /**
     * Group types by category (for preferences UI later).
     */
    public static function grouped(): array
    {
        return [
            'posts' => [
                self::POST_COMMENTED,
                self::COMMENT_REPLIED,
                self::BEST_ANSWER_SELECTED,
            ],
            'votes' => [
                self::POST_UPVOTED,
                self::POST_DOWNVOTED,
                self::COMMENT_UPVOTED,
                self::COMMENT_DOWNVOTED,
            ],
            'social' => [
                self::USER_FOLLOWED,
            ],
            'reputation' => [
                self::REPUTATION_CHANGED,
            ],
            'system' => [
                self::SYSTEM,
            ],
        ];
    }
}
