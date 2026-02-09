<?php

use App\Models\UserActivity;
use Illuminate\Support\Str;

function activity_description(App\Models\UserActivity $activity): string
{
    $target = $activity->target;

    return match ($activity->action) {
        'post_created' => 
            "Created a post: " . ($target ? "<a href='".route('posts.show', $target)."' class='font-bold hover:text-blue-600 transition'>".e($target->title)."</a>" : "<strong>a deleted post</strong>"),

        'comment_created' => 
            "Commented on " . ($target?->post ? "<a href='".route('posts.show', $target->post)."' class='font-bold hover:text-blue-600 transition'>".e($target->post->title)."</a>" : "<strong>a post</strong>"),

        'vote_up' => 
            "Upvoted a " . class_basename($target),

        'vote_down' => 
            "Downvoted a " . class_basename($target),

        'best_answer_selected' => 
            "Selected a best answer",

        'reputation_changed' => 
            "Reputation changed " . e($activity->details),

        default => 
            Illuminate\Support\Str::headline($activity->action),
    };
}
