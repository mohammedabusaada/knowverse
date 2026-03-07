<?php

namespace App\Observers;

use App\Models\{Vote, Post, Comment};
use App\Services\{ActivityService, NotificationService};
use App\Enums\NotificationType;

/**
 * Manages the platform's "Voting System".
 * Orchestrates reputation flow and peer-to-peer feedback loops. [cite: 45]
 */
class VoteObserver
{
    /**
     * Triggered upon new upvote or downvote.
     */
    public function created(Vote $vote): void
    {
        $this->applyVote($vote);
    }

/**
     * Intercepts state transitions (e.g., Upvote to Downvote).
     * Strictly reverts previous state before applying new economic impact. [cite: 48, 49, 50]
     */
    public function updated(Vote $vote): void
    {
        if ($vote->isDirty('value')) {
            $this->undoVote($vote, (int) $vote->getOriginal('value'));
            $this->applyVote($vote);
        }
    }

    /**
     * Triggered when a vote is completely retracted by the user.
     */
    public function deleted(Vote $vote): void
    {
        $this->undoVote($vote, (int) $vote->value);
    }

    // ==========================================================
    // Internal Core Logic
    // ==========================================================

    private function applyVote(Vote $vote): void
    {
        $target = $vote->target;
        
        // 1. Synchronize Cache: Update aggregate counts for UI consistency
        $target->updateVoteCounts();

        $owner  = $target->user;

        // 2. Anti-Gaming Guard: Prevent reputation manipulation via self-voting
        if ($vote->user_id === $owner->id) {
            return; // Halt process. No reputation or notification awarded.
        }

        // 3. Economy Update: Award reputation points
        $direction = $vote->value === 1 ? 'up' : 'down';
        $owner->addReputation($this->actionName($direction, $target), null, $target);

        // 4. Activity Audit & Notification
        ActivityService::voteCast($vote->user, $target, $vote->value);

        // 5. Dispatch Targeted Real-time Notification
        $notificationService = app(NotificationService::class);
        $type = $this->determineNotificationType($target, $vote->value);
        
        $notificationService->notify(
            recipient: $owner,
            type: $type,
            actor: $vote->user,
            target: $target
        );
    }

    private function undoVote(Vote $vote, int $previousValue): void
    {
        $target = $vote->target;
        
        // 1. ALWAYS Recalculate cache to maintain data integrity
        $target->updateVoteCounts();

        $owner  = $target->user;

        // 2. Anti-Gaming Guard
        if ($vote->user_id === $owner->id) {
            return;
        }

        // 3.Economic Rollback
        $direction = $previousValue === 1 ? 'up' : 'down';
        $owner->removeReputation($this->actionName($direction, $target), $target);

        // 4. Reconcile Activity logs
        ActivityService::voteRemoved($vote->user, $target);
    }

/**
     * Generates the configuration key for the reputation engine.
     * Consistently uses morph aliases ('post', 'comment').
     */
    private function actionName(string $direction, $target): string
    {
        return $target->getMorphClass() . "_{$direction}voted";
    }

    /**
     * Resolves the appropriate Enum Notification type based on polymorphic context.
     */
    private function determineNotificationType($target, int $value): NotificationType
    {
        if ($target instanceof Post) {
            return $value === 1 ? NotificationType::POST_UPVOTED : NotificationType::POST_DOWNVOTED;
        }
        
        return $value === 1 ? NotificationType::COMMENT_UPVOTED : NotificationType::COMMENT_DOWNVOTED;
    }
}