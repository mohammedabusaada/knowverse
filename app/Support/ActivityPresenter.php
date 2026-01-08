<?php

namespace App\Support;

use App\Models\UserActivity;
use Illuminate\Support\Str;

class ActivityPresenter
{
    public static function title(UserActivity $activity): string
    {
        return match ($activity->action) {
            'post_created'           => 'Created a post',
            'comment_created'        => 'Commented',
            'vote_up'                => 'Upvoted',
            'vote_down'              => 'Downvoted',
            'vote_removed'           => 'Removed a vote',
            'best_answer_selected'   => 'Selected a best answer',
            'reputation_changed'     => 'Reputation changed',
            'login'                  => 'Logged in',
            'logout'                 => 'Logged out',
            default                  => Str::headline($activity->action),
        };
    }

    public static function color(UserActivity $activity): string
    {
        return match ($activity->action) {
            'vote_up'            => 'text-green-600',
            'vote_down'          => 'text-red-600',
            'reputation_changed' => 'text-purple-600',
            default              => 'text-gray-900 dark:text-gray-100',
        };
    }

    public static function link(UserActivity $activity): ?string
    {
        $target = $activity->target;

        if (!$target) {
            return null;
        }

        return match (true) {
            $activity->action === 'post_created'
                => route('posts.show', $target),

            $activity->action === 'comment_created'
                && method_exists($target, 'post')
                => route('posts.show', $target->post) . "#comment-{$target->id}",

            default => null,
        };
    }

    public static function linkText(UserActivity $activity): ?string
    {
        $target = $activity->target;

        if (!$target) {
            return null;
        }

        return match (true) {
            property_exists($target, 'title') => Str::limit($target->title, 80),
            default                           => null,
        };
    }
}
