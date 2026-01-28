<?php

namespace App\Observers;

use App\Models\Vote;
use App\Models\Post;
use App\Models\Comment;
use App\Services\ActivityService;
use App\Services\NotificationService;
use App\Enums\NotificationType;

class VoteObserver
{
    public function created(Vote $vote): void
    {
        $this->applyVote($vote);
    }

    public function updated(Vote $vote): void
    {
        if ($vote->isDirty('value')) {
            $this->undoVote($vote, $vote->getOriginal('value'));
            $this->applyVote($vote);
        }
    }

    public function deleted(Vote $vote): void
    {
        $this->undoVote($vote, $vote->value);
    }

    // ==========================================================
    // INTERNAL
    // ==========================================================

    private function applyVote(Vote $vote): void
    {
        $target = $vote->target;
        $owner  = $target->user;

        // ----------------------------
        // Prevent self-vote notification
        // ----------------------------
        if ($vote->user_id === $owner->id) {
            return;
        }

        // ----------------------------
        // Reputation
        // ----------------------------
        if ($vote->value === 1) {
            $owner->addReputation(
                $this->actionName('up', $target),
                null,
                $target
            );
        }

        if ($vote->value === -1) {
            $owner->addReputation(
                $this->actionName('down', $target),
                null,
                $target
            );
        }

        // ----------------------------
        // Activity
        // ----------------------------
        ActivityService::voteCast(
            $vote->user,
            $target,
            $vote->value
        );

        // ----------------------------
        // Notification
        // ----------------------------
        $notificationService = app(NotificationService::class);

        $notificationService->notify(
            recipient: $owner,
            type: $target instanceof Post
                ? ($vote->value === 1
                    ? NotificationType::POST_UPVOTED
                    : NotificationType::POST_DOWNVOTED)
                : ($vote->value === 1
                    ? NotificationType::COMMENT_UPVOTED
                    : NotificationType::COMMENT_DOWNVOTED),
            actor: $vote->user,
            target: $target
        );

        $target->updateVoteCounts();
    }

    private function undoVote(Vote $vote, int $value): void
    {
        $target = $vote->target;
        $owner  = $target->user;

        if ($value === 1) {
            $owner->removeReputation(
                $this->actionName('up', $target),
                $target
            );
        }

        if ($value === -1) {
            $owner->removeReputation(
                $this->actionName('down', $target),
                $target
            );
        }

        ActivityService::voteRemoved(
            $vote->user,
            $target
        );

        $target->updateVoteCounts();
    }

    private function actionName(string $direction, $target): string
    {
        return $target instanceof Post
            ? "post_{$direction}voted"
            : "comment_{$direction}voted";
    }
}
