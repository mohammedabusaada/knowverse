<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tag;
use App\Enums\NotificationType;
use Illuminate\Auth\Access\AuthorizationException;

class FollowService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    // ==========================================================
    // User ↔ User
    // ==========================================================

    /**
     * Follow a user.
     *
     * @throws AuthorizationException
     */
    public function followUser(User $actor, User $target): void
    {
        // --------------------------------------------------
        // Guards
        // --------------------------------------------------
        if ($actor->id === $target->id) {
            throw new AuthorizationException('You cannot follow yourself.');
        }

        if ($actor->following()->whereKey($target->id)->exists()) {
            return;
        }

        // --------------------------------------------------
        // Persist
        // --------------------------------------------------
        $actor->following()->attach($target->id);

        // --------------------------------------------------
        // Activity
        // --------------------------------------------------
        ActivityService::userFollowedUser($actor, $target);

        // --------------------------------------------------
        // Notification (respects preferences automatically)
        // --------------------------------------------------
        $this->notifications->notify(
            recipient: $target,
            type: NotificationType::USER_FOLLOWED,
            actor: $actor
        );
    }

    /**
     * Unfollow a user.
     */
    public function unfollowUser(User $actor, User $target): void
    {
        $actor->following()->detach($target->id);
    }

    // ==========================================================
    // User ↔ Tag
    // ==========================================================

    /**
     * Follow a tag.
     */
    public function followTag(User $actor, Tag $tag): void
    {
        if ($actor->followedTags()->whereKey($tag->id)->exists()) {
            return;
        }

        // --------------------------------------------------
        // Persist
        // --------------------------------------------------
        $actor->followedTags()->attach($tag->id);

        // --------------------------------------------------
        // Activity
        // --------------------------------------------------
        ActivityService::userFollowedTag($actor, $tag);

        // --------------------------------------------------
        // Notification (if tag has an owner)
        // --------------------------------------------------
        if ($tag->user) {
            $this->notifications->notify(
                recipient: $tag->user,
                type: NotificationType::TAG_FOLLOWED,
                actor: $actor,
                target: $tag
            );
        }
    }

    /**
     * Unfollow a tag.
     */
    public function unfollowTag(User $actor, Tag $tag): void
    {
        $actor->followedTags()->detach($tag->id);
    }
}
