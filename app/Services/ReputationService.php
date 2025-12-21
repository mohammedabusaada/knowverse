<?php

namespace App\Services;

use App\Models\Reputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReputationService
{
    /**
     * Award reputation to a user for a specific action.
     *
     * @param User        $user
     * @param string      $action
     * @param int|null    $customDelta
     * @param Model|null  $source
     * @param string|null $note
     */
    public function award(
        User $user,
        string $action,
        ?int $customDelta = null,
        ?Model $source = null,
        ?string $note = null
    ): Reputation {

        // Determine the number of points to award
        $delta = $customDelta ?? config("reputation.points.$action", 0);

        // Create entry in reputation log
        $record = Reputation::create([
            'user_id'     => $user->id,
            'action'      => $action,
            'delta'       => $delta,
            'source_id'   => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'note'        => $note,
        ]);

        // Update cached total
        $user->increment('reputation_points', $delta);

        return $record;
    }

    /**
     * Remove reputation for a specific action + source.
     */
    public function remove(User $user, string $action, ?Model $source = null): void
{
    $query = Reputation::where('user_id', $user->id)
        ->where('action', $action);

    if ($source) {
        $query->where('source_id', $source->id)
              ->where('source_type', get_class($source));
    }

    // Get total delta removed
    $sum = $query->sum('delta');

    // Delete logs
    $query->delete();

    // Decrease user's cached reputation
    $user->decrement('reputation_points', $sum);
}


    /**
     * Recalculate user's total reputation from log table.
     */
    public function recalc(User $user): void
    {
        $total = Reputation::where('user_id', $user->id)->sum('delta');

        $user->update(['reputation_points' => $total]);
    }
}
