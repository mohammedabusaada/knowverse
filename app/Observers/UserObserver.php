<?php

namespace App\Observers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Oversees User lifecycle and default configuration provisioning. [cite: 37]
 */
class UserObserver
{
    /**
     * Initializes default Notification Preferences upon account provisioning. [cite: 38]
     * Ensures every new scholar has a standard experience from the first login. [cite: 39, 40]
     */
    public function created(User $user): void
    {
        foreach (config('notification-preferences.categories') as $type => $data) {
            NotificationPreference::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $type,
                ],
                [
                    'enabled' => $data['default'] ?? true,
                ]
            );
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Permanent erasure (right to be forgotten), before the row is deleted.
     *
     * The database cascade removes this user's own ledger entries, votes, activity and
     * notifications; discussions and comments they authored are kept with user_id set to
     * null. Reputation that other users earned from this user's votes is deliberately
     * retained: those ledger entries reference only the recipient and the voted content,
     * never the voter, so they hold no personal data about the erased user, and reversing
     * them would penalise people for someone else's deletion.
     *
     * The cascade bypasses VoteObserver, which would leave the vote counters on the
     * affected content stale, so the voted targets are captured here and recalculated
     * once the votes are gone.
     */
    public function forceDeleting(User $user): void
    {
        static::$pendingVoteTargets[$user->id] = Vote::where('user_id', $user->id)
            ->get(['target_type', 'target_id'])
            ->map(fn (Vote $vote) => [$vote->target_type, (int) $vote->target_id])
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Permanent erasure, after the row and its cascaded records are gone.
     */
    public function forceDeleted(User $user): void
    {
        $targets = static::$pendingVoteTargets[$user->id] ?? [];
        unset(static::$pendingVoteTargets[$user->id]);

        foreach ($targets as [$type, $id]) {
            $model = Relation::getMorphedModel($type) ?? $type;
            $model::withoutGlobalScopes()->find($id)?->updateVoteCounts();
        }
    }

    /**
     * Vote targets of users being erased, captured before the cascade removes their
     * votes. Keyed by user id: observers are resolved per event, so this cannot be an
     * instance property.
     *
     * @var array<int, array<int, array{0: string, 1: int}>>
     */
    private static array $pendingVoteTargets = [];
}
