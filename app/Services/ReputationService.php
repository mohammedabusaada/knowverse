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

            $query = Reputation::where('user_id', $user->id)
                ->where('action', $action);

            // Scope the removal specifically to a polymorphic entity if contextualized
            if ($source) {
                $query->where('source_id', $source->getKey())
                      ->where('source_type', $source->getMorphClass());
            }

            // Retrieve only the LATEST single ledger entry for this action/source combination.
            // Deleting all matching rows would destroy the points earned from OTHER users' votes.
            $record = $query->latest('id')->first();

            if (!$record) {
                return;
            }

            $sum = (int) $record->delta;

            // 1. Ledger Cleanup: Purge the targeted transactional entries
            $query->delete();

            // 2. Cache Reconcile: Adjust the user's aggregate standing
            $user->decrement('reputation_points', $sum);

            // 3. Transparency Audit: Log the reversal action
            ActivityService::reputationChanged($user, -$sum, $source, "{$action}_reverted");
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