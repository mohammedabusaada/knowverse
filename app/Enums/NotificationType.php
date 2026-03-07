<?php

namespace App\Enums;

/**
 * Defines the taxonomy of all possible events that trigger a notification within the platform.
 * Utilizing a backed Enum ensures strict type checking and prevents arbitrary string inputs
 * when generating or filtering notifications.
 */
enum NotificationType: string
{
    // Post & Comment Activity
    case POST_COMMENTED = 'post_commented';
    case COMMENT_REPLIED = 'comment_replied';
    case AUTHORS_PICK_RECEIVED = 'authors_pick_received';
    
    // Moderation & Trust
    case REPORT_RESOLVED = 'report_resolved';
    case CONTENT_REMOVED = 'content_removed';

    // Voting Mechanics
    case POST_UPVOTED = 'post_upvoted';
    case POST_DOWNVOTED = 'post_downvoted';
    case COMMENT_UPVOTED = 'comment_upvoted';
    case COMMENT_DOWNVOTED = 'comment_downvoted';

    // Social Graph
    case USER_FOLLOWED = 'user_followed';
    case TAG_FOLLOWED  = 'tag_followed';
    case NEW_POST_FOLLOWING = 'new_post_following';
    case NEW_POST_TAG = 'new_post_tag';

    // System & Global Operations
    case REPUTATION_CHANGED = 'reputation_changed';
    case SYSTEM = 'system';

    /**
     * Identifies critical system and moderation notifications.
     * Mandatory notifications bypass user opt-out preferences to ensure essential
     * administrative communication always reaches the user.
     * * @return bool
     */
    public function isMandatory(): bool
    {
        return match ($this) {
            self::SYSTEM, 
            self::REPORT_RESOLVED, 
            self::CONTENT_REMOVED => true,
            default => false,
        };
    }

    /**
     * Categorizes notification types logically for the user preference settings UI.
     * This allows users to toggle entire groups of notifications at once.
     * * @return array<string, array<NotificationType>>
     */
    public static function grouped(): array
    {
        return [
            'posts' => [
                self::POST_COMMENTED,
                self::COMMENT_REPLIED,
                self::AUTHORS_PICK_RECEIVED,
            ],
            'votes' => [
                self::POST_UPVOTED,
                self::POST_DOWNVOTED,
                self::COMMENT_UPVOTED,
                self::COMMENT_DOWNVOTED,
            ],
            'social' => [
                self::USER_FOLLOWED,
                self::TAG_FOLLOWED,
                self::NEW_POST_FOLLOWING,
                self::NEW_POST_TAG,
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