<?php

namespace App\Observers;

use App\Models\Vote;

class VoteObserver
{
    /**
     * When a vote is first created.
     */
    public function created(Vote $vote)
    {
        $this->applyNewVoteReputation($vote);
        $vote->target->updateVoteCounts();
    }

    /**
     * When a vote changes (up → down, or vote → unvote).
     */
    public function updated(Vote $vote)
    {
        if ($vote->isDirty('value')) {

            $old = $vote->getOriginal('value'); // previous value
            $new = $vote->value;                // new value

            // 1. Undo old reputation
            $this->undoVoteReputation($vote, $old);

            // 2. Apply new reputation (if any)
            $this->applyNewVoteReputation($vote);
        }

        $vote->target->updateVoteCounts();
    }

    /**
     * When a vote is removed entirely.
     */
    public function deleted(Vote $vote)
    {
        $this->undoVoteReputation($vote, $vote->value);
        $vote->target->updateVoteCounts();
    }


    // ============================================================
    //  REPUTATION LOGIC
    // ============================================================

    /**
     * Apply reputation for a newly applied vote (created or updated).
     */
    private function applyNewVoteReputation(Vote $vote): void
    {
        $target = $vote->target;
        $owner  = $target->user;

        // Upvote applied
        if ($vote->value === 1) {
            $owner->addReputation(
                $this->getActionName('up', $target),
                null,
                $target
            );
        }

        // Downvote applied
        elseif ($vote->value === -1) {
            $owner->addReputation(
                $this->getActionName('down', $target),
                null,
                $target
            );
        }
    }

    /**
     * Undo reputation from the previous vote value.
     */
    private function undoVoteReputation(Vote $vote, int $oldValue): void
    {
        $target = $vote->target;

        // Undo old upvote
        if ($oldValue === 1) {
            $vote->target->user->removeReputation(
                $this->getActionName('up', $target),
                $target
            );
        }

        // Undo old downvote
        elseif ($oldValue === -1) {
            $vote->target->user->removeReputation(
                $this->getActionName('down', $target),
                $target
            );
        }
    }

    /**
     * Determine action names based on target type.
     *
     * post_upvoted
     * post_downvoted
     * comment_upvoted
     * comment_downvoted
     */
    private function getActionName(string $direction, $target): string
    {
        $type = $target instanceof \App\Models\Post ? 'post' : 'comment';

        return $type . "_voted_" . $direction;
    }
}
