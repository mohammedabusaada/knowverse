<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\{ActivityService, ContentModerationService};
use Illuminate\Validation\ValidationException;

/**
 * Governs the lifecycle of Scholarly Discussions (Posts).
 * Enforces editorial standards and manages the platform's contribution economy.
 */
class PostObserver
{
    public function __construct(protected ContentModerationService $moderationService) {}

/**
     * Editorial Gatekeeper: Validates meta-data and body content against security filters.
     */
    public function saving(Post $post): void
    {
        // Integrity Check: Filter discussion titles
        if ($this->moderationService->containsBlockedWords($post->title)) {
            throw ValidationException::withMessages(['title' => 'The title contains prohibited language.']);
        }

        // Integrity Check: Filter discussion body
        if ($this->moderationService->containsBlockedWords($post->body)) {
            throw ValidationException::withMessages(['body' => 'The discussion body contains prohibited language.']);
        }
    }

/**
     * Finalize discussion provisioning by logging and rewarding the contributor. [cite: 26, 27, 28]
     */
    public function created(Post $post): void
    {
        $author = $post->user;
        
        // Log the creation activity in the system
        ActivityService::postCreated($author, $post);
        
        // Award reputation points to the author for their contribution
        $author->addReputation('post_created', null, $post);
        
        // // Note: Global notifications are offloaded to background Jobs for performance
    }

/**
     * Audit Trail Reconcile: Synchronize reputation data upon discussion removal. 
     */
    public function deleting(Post $post): void
    {
        $author = $post->user;
        
        // 1. Revert creation points
        $author->removeReputation('post_created', $post);

        // 2. Revert 'Author Pick' rewards if applicable
        if ($post->bestComment) {
            $author->removeReputation('authors_pick_awarded', $post->bestComment);
            $post->bestComment->user->removeReputation('authors_pick_received', $post->bestComment);
        }

        // 3. Rollback all reputation changes triggered by community votes
        foreach ($post->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1 ? 'post_upvoted' : 'post_downvoted',
                $post
            );
        }
    }
}