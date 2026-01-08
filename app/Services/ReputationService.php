<?php

namespace App\Services;

use App\Models\Reputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityService;

class ReputationService
{
    /**
     * Award reputation to a user for a specific action.
     */
    public function award(
        User $user,
        string $action,
        ?int $customDelta = null,
        ?Model $source = null,
        ?string $note = null
    ): Reputation {
        return DB::transaction(function () use (
            $user,
            $action,
            $customDelta,
            $source,
            $note
        ) {

            $delta = $customDelta
                ?? config("reputation.points.$action", 0);

            // Create reputation log entry
            $record = Reputation::create([
                'user_id'     => $user->id,
                'action'      => $action,
                'delta'       => $delta,
                'source_id'   => $source?->id,
                'source_type' => $source ? get_class($source) : null,
                'note'        => $note,
            ]);

            // Update cached reputation
            if ($delta !== 0) {
                $user->increment('reputation_points', $delta);
            }

            // Log activity (single source of truth)
            ActivityService::reputationChanged(
                $user,
                $delta,
                $source,
                $action
            );

            return $record;
        });
    }

    /**
     * Remove reputation for a specific action (+ optional source).
     */
    public function remove(
        User $user,
        string $action,
        ?Model $source = null
    ): void {
        DB::transaction(function () use ($user, $action, $source) {

            $query = Reputation::where('user_id', $user->id)
                ->where('action', $action);

            if ($source) {
                $query->where('source_id', $source->id)
                      ->where('source_type', get_class($source));
            }

            $sum = (int) $query->sum('delta');

            if ($sum === 0) {
                return;
            }

            // Delete reputation logs
            $query->delete();

            // Update cached reputation
            $user->decrement('reputation_points', $sum);

            // Log reversal as activity
            ActivityService::reputationChanged(
                $user,
                -$sum,
                $source,
                "{$action}_reverted"
            );
        });
    }

    /**
     * Recalculate user's reputation from logs (admin/debug tool).
     */
    public function recalc(User $user): void
    {
        DB::transaction(function () use ($user) {

            $total = Reputation::where('user_id', $user->id)
                ->sum('delta');

            $user->update([
                'reputation_points' => $total,
            ]);
        });
    }
}
