<?php

use App\Models\UserActivity;
use Illuminate\Support\Str;
use App\Models\Post;

function activity_description(App\Models\UserActivity $activity): string
{
    $target = $activity->target;

    return match ($activity->action) {
        'post_created' => 
            "Created a discussion: " . ($target ? "<a href='".route('posts.show', $target)."' class='font-bold hover:text-blue-600 transition'>".e($target->title)."</a>" : "<strong>a deleted discussion</strong>"),

        'comment_created' => 
            "Commented on " . ($target?->post ? "<a href='".route('posts.show', $target->post)."' class='font-bold hover:text-blue-600 transition'>".e($target->post->title)."</a>" : "<strong>a discussion</strong>"),

        'vote_up' => 
            "Upvoted a " . ($target instanceof Post ? 'discussion' : 'comment'),

        'vote_down' => 
            "Downvoted a " . ($target instanceof Post ? 'discussion' : 'comment'),

        'authors_pick_selected' => 
            "Highlighted a comment as Author's Pick",

        'reputation_changed' => 
            "Reputation changed " . e($activity->details),

        default => 
            Illuminate\Support\Str::headline($activity->action),
    };
}
