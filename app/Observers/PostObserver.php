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
        // Screen content ONLY when the editorial fields changed. Counter/state updates
        // (vote tallies, best_comment_id, view_count, is_hidden) must not re-run the
        // moderation filter or the platform-wide duplicate check — doing so wastes a
        // full-table query on every vote and can reject legitimate state changes.
        if (! $post->isDirty('title') && ! $post->isDirty('body')) {
            return;
        }

        // 1. Integrity Check: Filter discussion titles
        if ($this->moderationService->containsBlockedWords($post->title)) {
            throw ValidationException::withMessages(['title' => 'The title contains prohibited language.']);
        }

        // 2. Integrity Check: Filter discussion body
        if ($this->moderationService->containsBlockedWords($post->body)) {
            throw ValidationException::withMessages(['body' => 'The discussion body contains prohibited language.']);
        }

        // 3. Permanent Duplicate Prevention: Ensure unique content across the platform
        $duplicateExists = Post::where('title', $post->title)
            ->where('body', $post->body)
            // Exclude the current post if the user is editing it
            ->when($post->exists, function ($query) use ($post) {
                return $query->where('id', '!=', $post->id);
            })
            ->exists();

        if ($duplicateExists) {
            throw ValidationException::withMessages([
                'title' => 'This exact discussion has already been published. Please contribute to the existing thread.'
            ]);
        }
    }

    /**
     * Finalize discussion provisioning by logging and rewarding the contributor.
     */
    public function created(Post $post): void
    {
        $author = $post->user;
        
        // Log the creation activity in the system
        ActivityService::postCreated($author, $post);
        
        // Award reputation points to the author for their contribution
        $author->addReputation('post_created', null, $post);
        
        // Note: Global notifications are offloaded to background Jobs for performance
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