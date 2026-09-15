<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Vote extends Model
{
    use HasFactory;

    // Standard updated_at is unnecessary for immutable voting logs
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'value', // 1 for Upvote, -1 for Downvote
    ];

    protected $casts = [
        'value' => 'integer',
        'created_at' => 'datetime',
    ];

    // ==============================================================================
    // Relationships
    // ==============================================================================

    /**
     * The scholar who cast the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The entity being voted on (Post or Comment).
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    // ==============================================================================
    // Utility & Domain Logic
    // ==============================================================================

    /**
     * Cast or update a vote for a specific target.
     *
     * The vote and everything its observer does (vote counters, reputation ledger,
     * activity log, notification) commit together or not at all, and the whole unit is
     * retried if the database aborts it with a deadlock.
     *
     * * @param User $user The voter
     * @param  Model  $target  The polymorphic target (Post/Comment)
     * @param  int  $value  The vote weight (1 or -1)
     */
    public static function castVote(User $user, Model $target, int $value): self
    {
        return static::lockedForTarget($user, $target, function (?self $vote) use ($user, $target, $value) {
            if ($vote) {
                // An unchanged value is not dirty, so a repeated request fires no observer.
                $vote->update(['value' => $value]);

                return $vote;
            }

            return static::create([
                'user_id' => $user->id,
                'target_id' => $target->getKey(),
                'target_type' => $target->getMorphClass(),
                'value' => $value,
            ]);
        });
    }

    /**
     * Retract a user's vote on a target, if one exists. Atomic in the same way as castVote().
     */
    public static function retract(User $user, Model $target): void
    {
        static::lockedForTarget($user, $target, fn (?self $vote) => $vote?->delete());
    }

    /**
     * Runs a vote change inside a transaction that holds a row lock on the target, then
     * on the user's existing vote. Concurrent votes on the same target are serialised, so
     * two simultaneous identical requests cannot both apply their side effects, and the
     * target's counters are recounted only after earlier votes have committed.
     */
    private static function lockedForTarget(User $user, Model $target, callable $change): mixed
    {
        return DB::transaction(function () use ($user, $target, $change) {
            $target->newQueryWithoutScopes()->whereKey($target->getKey())->lockForUpdate()->first();

            $vote = static::where('user_id', $user->id)
                ->where('target_id', $target->getKey())
                ->where('target_type', $target->getMorphClass())
                ->lockForUpdate()
                ->first();

            return $change($vote);
        }, 5);
    }
}
