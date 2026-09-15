<?php

namespace App\Services;

use App\Models\Reputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Gamification and Reputation Economy Manager.
 * Implements a rigid ledger system to trace every point awarded or retracted,
 * ensuring absolute integrity of user standing.
 */
class ReputationService
{
    /**
     * Issues an economic reward or penalty.
     * Wrapped in a DB transaction to guarantee atomicity (All operations succeed simultaneously, or none do).
     */
    public function award(
        User $user,
        string $action,
        ?int $customDelta = null,
        ?Model $source = null,
        ?string $note = null
    ): Reputation {
        return DB::transaction(function () use ($user, $action, $customDelta, $source, $note) {
            $this->lockLedger($user);

            // Resolve dynamic point configurations from the central settings file
            $delta = $customDelta ?? config("reputation.points.$action", 0);

            // 1. Immutable Audit: Append entry to the append-only ledger
            $record = Reputation::create([
                'user_id' => $user->id,
                'action' => $action,
                'delta' => $delta,
                'source_id' => $source?->getKey(),
                'source_type' => $source ? $source->getMorphClass() : null,
                'note' => $note,
            ]);

            // 2. Cache Synchronization: Update the aggregated column on the User entity for fast querying
            if ($delta !== 0) {
                $user->increment('reputation_points', $delta);
            }

            // 3. Propagate to the Public Activity Stream
            ActivityService::reputationChanged($user, $delta, $source, $action);

            return $record;
        });
    }

    /**
     * Retracts reputation points previously distributed.
     * Crucial for restoring economic equilibrium when content is soft-deleted or downvoted.
     *
     * Reversal is idempotent per ledger entry: each call reverses the most recent entry for
     * (user, action, source) that has not already been reversed, and does nothing when none
     * remains. A unique index on reputations.reverses_id enforces this in the database
     * itself, so the same entry can never be reversed twice, even by concurrent requests.
     */
    public function remove(User $user, string $action, ?Model $source = null): void
    {
        // If a concurrent request reverses the same entry first, the unique index rejects
        // this insert. Retry so the call moves on to the next outstanding entry, if any.
        for ($attempt = 1; ; $attempt++) {
            try {
                DB::transaction(fn () => $this->reverseLatestOutstanding($user, $action, $source), 3);

                return;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === 3) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Appends a compensating entry for the latest ledger entry that has not been reversed.
     */
    private function reverseLatestOutstanding(User $user, string $action, ?Model $source): void
    {
        $this->lockLedger($user);

        $query = Reputation::where('user_id', $user->id)
            ->where('action', $action)
            ->whereDoesntHave('reversal');

        if ($source) {
            $query->where('source_id', $source->getKey())
                ->where('source_type', $source->getMorphClass());
        }

        // Lock the candidate so a concurrent reversal waits instead of selecting it too.
        $record = $query->latest('id')->lockForUpdate()->first();

        if (! $record) {
            return; // Nothing outstanding: a repeated reversal is a no-op.
        }

        $delta = (int) $record->delta;

        // APPEND-ONLY INTEGRITY: the original entry is never modified or deleted. The
        // compensating entry references it, and the system invariant is preserved exactly:
        //     user.reputation_points === SUM(reputations.delta WHERE user_id = user)
        Reputation::record(
            $user->id,
            "{$action}_reverted",
            -$delta,
            $source,
            "Reversal of ledger entry #{$record->id}",
            $record->id
        );

        // Cache reconcile: move the denormalised aggregate by the same magnitude.
        if ($delta !== 0) {
            $user->decrement('reputation_points', $delta);
        }

        // Transparency audit.
        ActivityService::reputationChanged($user, -$delta, $source, "{$action}_reverted");
    }

    /**
     * Serialises ledger writes per user. Every award, reversal and recalculation first
     * takes a row lock on the user, so concurrent ledger writes for the same user run one
     * after another instead of deadlocking on the ledger's index gaps.
     */
    private function lockLedger(User $user): void
    {
        DB::table('users')->where('id', $user->id)->lockForUpdate()->value('id');
    }

    /**
     * Diagnostic and Recovery Tool.
     * Rehydrates (recalculates) the user's aggregate reputation score from the ground up
     * by summarizing all historical ledger transactions.
     */
    public function recalc(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->lockLedger($user);
            $total = (int) Reputation::where('user_id', $user->id)->sum('delta');
            $user->update(['reputation_points' => $total]);
        });
    }
}
