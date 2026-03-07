<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

/**
 * Centralized Activity Logger (Audit Trail).
 * Acts as the single source of truth for tracking domain events and user actions across the platform.
 */
class ActivityService
{
/**
     * Core persistence method for logging activities.
     * Utilizes polymorphic relations to dynamically link actions to diverse entities.
     */
    public static function log(
        User $user,
        string $action,
        ?Model $target = null,
        ?string $details = null
    ): UserActivity {
        return UserActivity::create([
            'user_id'     => $user->id,
            'action'      => $action,
            'target_id'   => $target?->id,
            'target_type' => $target ? get_class($target) : null,
            'details'     => $details,
        ]);
    }

    // ==========================================================
    //  DOMAIN EVENTS (Action Specific Loggers)
    // ==========================================================

    public static function postCreated(User $user, Model $post): void
    {
        self::log(
            $user,
            'post_created',
            $post,
            $post->title
        );
    }

    public static function commentCreated(User $user, Model $comment): void
    {
        self::log(
            $user,
            'comment_created',
            $comment,
            str($comment->body)->limit(80)
        );
    }

    public static function voteCast(User $user, Model $target, int $value): void
    {
        self::log(
            $user,
            $value === 1 ? 'vote_up' : 'vote_down',
            $target
        );
    }

    public static function voteRemoved(User $user, Model $target): void
    {
        self::log(
            $user,
            'vote_removed',
            $target
        );
    }

public static function authorsPickSelected(User $user, Model $comment): void
    {
        self::log(
            $user,
            'authors_pick_selected',
            $comment
        );
    }


    // --------------------------------------------------
    // Follow events
    // --------------------------------------------------

    public static function userFollowedUser(User $actor, User $target): void
    {
        self::log(
            $actor,
            'user_followed_user',
            $target
        );
    }

    public static function userFollowedTag(User $actor, Tag $tag): void
    {
        self::log(
            $actor,
            'user_followed_tag',
            $tag,
            $tag->name
        );
    }

/**
     * Hook to log changes in a scholar's academic standing.
     * Invoked automatically by the ReputationService to ensure data consistency.
     */
    public static function reputationChanged(
        User $user,
        int $delta,
        ?Model $source = null,
        ?string $action = null
    ): void {
        if ($delta === 0) {
            return; // Ignore zero-impact transactions to prevent database bloat
        }

        self::log(
            $user,
            'reputation_changed',
            $source,
            trim(($action ?? 'adjustment') . " ({$delta})")
        );
    }

    public static function login(User $user): void
    {
        self::log($user, 'user_login');
    }

    public static function logout(User $user): void
    {
        self::log($user, 'user_logout');
    }
}
