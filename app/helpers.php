<?php

use App\Models\UserActivity;
use Illuminate\Support\Str;

function activity_description(UserActivity $activity): string
{
    $user = e($activity->user->display_name);
    $target = $activity->target;

    return match ($activity->action) {

        'post_created' =>
            "Created a post: <strong>{$target->title}</strong>",

        'comment_created' =>
            "Commented on <strong>{$target->post->title}</strong>",

        'vote_up' =>
            "Upvoted a " . class_basename($target),

        'vote_down' =>
            "Downvoted a " . class_basename($target),

        'best_answer_selected' =>
            "Selected a best answer",

        'reputation_changed' =>
            "Reputation changed {$activity->details}",

        default =>
            Str::headline($activity->action),
    };
}
