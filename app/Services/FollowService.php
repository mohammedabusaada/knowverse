<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tag;
use App\Enums\NotificationType;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Manages the platform's Social Graph interactions (Following Users/Tags).
 * Handles relationship persistence, idempotent operations, and event propagation.
 */
class FollowService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    // ==========================================================
    // User ↔ User Subgraph
    // ==========================================================

/**
     * Establishes a directional connection between two scholars.
     * @throws AuthorizationException
     */
    public function followUser(User $actor, User $target): void
    {
        // 1. Architectural Guard: Prevent self-referential relationships
        if ($actor->id === $target->id) {
            throw new AuthorizationException('You cannot follow yourself.');
        }

        // 2. Idempotency Check: Halt if the relationship already exists
        if ($actor->following()->whereKey($target->id)->exists()) {
            return;
        }

        // 3. Persist Edge in the Social Graph
        $actor->following()->attach($target->id);

        // 4. Propagate State Changes (Activity & Notification)
        ActivityService::userFollowedUser($actor, $target);

        $this->notifications->notify(
            recipient: $target,
            type: NotificationType::USER_FOLLOWED,
            actor: $actor
        );
    }

/**
     * Severs an existing user-to-user connection.
     */
    public function unfollowUser(User $actor, User $target): void
    {
        $actor->following()->detach($target->id);
    }

    // ==========================================================
    // User ↔ Tag Subgraph
    // ==========================================================

/**
     * Subscribes a user to a specific taxonomy/topic stream.
     */
    public function followTag(User $actor, Tag $tag): void
    {
        // Idempotency check to prevent duplicate pivot rows
        if ($actor->followedTags()->whereKey($tag->id)->exists()) {
            return;
        }

        // Persist Subscription
        $actor->followedTags()->attach($tag->id);

        // Propagate State Changes
        ActivityService::userFollowedTag($actor, $tag);

        // Notify the topic's creator if applicable
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
     * Unsubscribes a user from a topic stream.
     */
    public function unfollowTag(User $actor, Tag $tag): void
    {
        $actor->followedTags()->detach($tag->id);
    }
}
