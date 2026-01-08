<?php

namespace App\Observers;

use App\Models\Vote;
use App\Services\ActivityService;
use App\Models\Post;
use App\Models\Comment;

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

        ActivityService::voteCast(
            $vote->user,
            $target,
            $vote->value
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
            ? "post_voted_{$direction}"
            : "comment_voted_{$direction}";
    }
}
