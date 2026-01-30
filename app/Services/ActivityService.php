<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;


class ActivityService
{
    /**
     * Core activity logger.
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
    //  DOMAIN EVENTS
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

    public static function bestAnswerSelected(User $user, Model $comment): void
    {
        self::log(
            $user,
            'best_answer_selected',
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
     * Reputation change hook (called by ReputationService).
     */
    public static function reputationChanged(
        User $user,
        int $delta,
        ?Model $source = null,
        ?string $action = null
    ): void {
        if ($delta === 0) {
            return;
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
