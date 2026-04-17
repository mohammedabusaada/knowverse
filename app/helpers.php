<?php

use App\Models\UserActivity;
use Illuminate\Support\Str;
use App\Models\Post;

function activity_description(UserActivity $activity): string
{
    $target = $activity->target;

    return match ($activity->action) {
        'post_created' => 
            "Created a discussion: " . ($target ? "<a href='".route('posts.show', $target)."' class='font-bold hover:text-accent transition'>".e($target->title)."</a>" : "<strong>a deleted discussion</strong>"),

        'comment_created' => 
            "Commented on " . ($target?->post ? "<a href='".route('posts.show', $target->post)."' class='font-bold hover:text-accent transition'>".e($target->post->title)."</a>" : "<strong>a discussion</strong>"),

        'vote_up' => 
            "Upvoted a " . ($target instanceof Post ? 'discussion' : 'comment'),

        'vote_down' => 
            "Downvoted a " . ($target instanceof Post ? 'discussion' : 'comment'),

        'authors_pick_selected' => 
            "Highlighted a comment as Author's Pick",

        'reputation_changed' => (function () use ($activity) {
            $details = (string) $activity->details;
            
            preg_match('/\((.*?)\)/', $details, $matches);
            $points = $matches[1] ?? '';
            
            $pointText = $points ? " (<strong class='text-ink'>{$points} points</strong>)" : "";

            return match (true) {
                str_contains($details, 'post_created') => "Earned reputation for publishing a discussion" . $pointText,
                str_contains($details, 'comment_created') => "Earned reputation for contributing a comment" . $pointText,
                str_contains($details, 'post_upvoted') => "Received an upvote on a discussion" . $pointText,
                str_contains($details, 'comment_upvoted') => "Received an upvote on a comment" . $pointText,
                str_contains($details, 'post_downvoted') => "Lost reputation due to a downvoted discussion" . $pointText,
                str_contains($details, 'comment_downvoted') => "Lost reputation due to a downvoted comment" . $pointText,
                str_contains($details, 'authors_pick_awarded') => "Rewarded for highlighting an Author's Pick" . $pointText,
                str_contains($details, 'authors_pick_received') => "Earned reputation for receiving an Author's Pick" . $pointText,
                default => "Reputation adjusted" . $pointText,
            };
        })(),

        default => 
            Str::headline($activity->action),
    };
}