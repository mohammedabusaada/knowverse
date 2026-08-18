<?php

namespace App\Services;

use App\Models\Reputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityService;

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
            
            // Resolve dynamic point configurations from the central settings file
            $delta = $customDelta ?? config("reputation.points.$action", 0);

            // 1. Immutable Audit: Append entry to the append-only ledger
            $record = Reputation::create([
                'user_id'     => $user->id,
                'action'      => $action,
                'delta'       => $delta,
                'source_id'   => $source?->getKey(),
                'source_type' => $source ? $source->getMorphClass() : null,
                'note'        => $note,
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
     */
    public function remove(User $user, string $action, ?Model $source = null): void
    {
        DB::transaction(function () use ($user, $action, $source) {

            // Locate the most recent ORIGINAL award for this (user, action, source).
            // Reversal rows are stored under "{action}_reverted", so they are never
            // matched here — guaranteeing each call neutralises exactly ONE prior
            // contribution (e.g. one retracted upvote), even when several scholars
            // have voted on the same target and produced multiple identical entries.
            $query = Reputation::where('user_id', $user->id)
                ->where('action', $action);

            if ($source) {
                $query->where('source_id', $source->getKey())
                      ->where('source_type', $source->getMorphClass());
            }

            $record = $query->latest('id')->first();

            if (!$record) {
                return;
            }

            $delta = (int) $record->delta;

            // APPEND-ONLY INTEGRITY: a ledger entry is never deleted. Instead we append
            // a compensating (negative) transaction, so the ledger remains a complete,
            // immutable audit trail and the system invariant is preserved exactly:
            //     user.reputation_points === SUM(reputations.delta WHERE user_id = user)
            Reputation::record(
                $user->id,
                "{$action}_reverted",
                -$delta,
                $source,
                "Reversal of ledger entry #{$record->id}"
            );

            // Cache reconcile: move the denormalised aggregate by the same magnitude.
            if ($delta !== 0) {
                $user->decrement('reputation_points', $delta);
            }

            // Transparency audit.
            ActivityService::reputationChanged($user, -$delta, $source, "{$action}_reverted");
        });
    }

/**
     * Diagnostic and Recovery Tool.
     * Rehydrates (recalculates) the user's aggregate reputation score from the ground up 
     * by summarizing all historical ledger transactions.
     */
    public function recalc(User $user): void
    {
        DB::transaction(function () use ($user) {
            $total = (int) Reputation::where('user_id', $user->id)->sum('delta');
            $user->update(['reputation_points' => $total]);
        });
    }
}