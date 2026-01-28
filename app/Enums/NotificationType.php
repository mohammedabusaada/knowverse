<?php

namespace App\Enums;

enum NotificationType: string
{
    // Post & Comment
    case POST_COMMENTED = 'post_commented';
    case COMMENT_REPLIED = 'comment_replied';
    case BEST_ANSWER_RECEIVED = 'best_answer_received';

        // Votes
    case POST_UPVOTED = 'post_upvoted';
    case POST_DOWNVOTED = 'post_downvoted';
    case COMMENT_UPVOTED = 'comment_upvoted';
    case COMMENT_DOWNVOTED = 'comment_downvoted';

        // Social
    case USER_FOLLOWED = 'user_followed';

        // Reputation
    case REPUTATION_CHANGED = 'reputation_changed';

        // System
    case SYSTEM = 'system';

    /**
     * Determine if the notification is mandatory (bypasses preferences).
     */
    public function isMandatory(): bool
    {
        return match ($this) {
            self::SYSTEM => true,
            default => false,
        };
    }

    /**
     * Group types by category (replacing your old grouped() helper).
     */
    public static function grouped(): array
    {
        return [
            'posts' => [
                self::POST_COMMENTED,
                self::COMMENT_REPLIED,
                self::BEST_ANSWER_RECEIVED,
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
