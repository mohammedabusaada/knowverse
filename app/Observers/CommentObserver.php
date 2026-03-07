<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\{ActivityService, NotificationService, ContentModerationService};
use App\Enums\NotificationType;
use Illuminate\Validation\ValidationException;

/**
 * Monitors the lifecycle of Scholarly Responses (Comments).
 * Orchestrates content moderation, reputation rewarding, and notification dispatching.
 */
class CommentObserver
{
    public function __construct(protected ContentModerationService $moderationService) {}

/**
     * Intercept the saving event (Create/Update) for content sanitization.
     * Acts as a final security gate to enforce community guidelines before persistence. [cite: 3, 4]
     */
    public function saving(Comment $comment): void
    {
        // Content Integrity: Prevent prohibited language from entering the database
        if ($this->moderationService->containsBlockedWords($comment->body)) {
            throw ValidationException::withMessages([
                'body' => 'Your response contains language that violates community guidelines.'
            ]);
        }
    }

/**
     * Finalize the creation process by rewarding the author and notifying relevant peers.
     */
    public function created(Comment $comment): void
    {
        $author = $comment->user;
        
        // Reputation System: Log activity and award contribution points
        ActivityService::commentCreated($author, $comment);
        $author->addReputation('comment_created', null, $comment);

        $notificationService = app(NotificationService::class);
        $postAuthor = $comment->post->user;

        // Social Logic: Notify the discussion owner (if they are not the actor)
        if ($postAuthor->id !== $author->id) {
            $notificationService->notify(
                recipient: $postAuthor,
                type: NotificationType::POST_COMMENTED,
                actor: $author,
                target: $comment
            );
        }

        // Threaded Logic: Notify the parent comment author for nested replies
        if ($comment->parent) {
            $parentAuthor = $comment->parent->user;
            // Optimization: Avoid redundant notifications if parent author is already notified as post owner
            if ($parentAuthor->id !== $author->id && $parentAuthor->id !== $postAuthor->id) {
                $notificationService->notify(
                    recipient: $parentAuthor,
                    type: NotificationType::COMMENT_REPLIED,
                    actor: $author,
                    target: $comment
                );
            }
        }
    }

    /**
     * Handle the Soft-Deletion cleanup.
     * Reverts all reputation gains associated with this comment to maintain data integrity.
     */
    public function deleting(Comment $comment): void
    {
        $author = $comment->user;
        
        // 1. Transactional Rollback: Revert base contribution points
        $author->removeReputation('comment_created', $comment);

        // 2. Critical Reference Check: Nullify 'Best Answer' links in parent posts
        if ($comment->post?->best_comment_id === $comment->id) {
            // Update reference silently without re-triggering Post Observers
            $comment->post->update(['best_comment_id' => null]);
            
            // Retract bonus points from both the receiver and the rewarder
            $author->removeReputation('authors_pick_received', $comment);
            $comment->post->user->removeReputation('authors_pick_awarded', $comment);
        }

        // 3. Social Reconcile: Rollback all points gained/lost via community votes
        foreach ($comment->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1 ? 'comment_upvoted' : 'comment_downvoted',
                $comment
            );
        }
    }
}