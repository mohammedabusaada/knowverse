<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

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
        'value'      => 'integer',
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
     * * @param User $user The voter
     * @param Model $target The polymorphic target (Post/Comment)
     * @param int $value The vote weight (1 or -1)
     */
    public static function castVote(User $user, Model $target, int $value): self
    {
        return static::updateOrCreate(
            [
                'user_id'     => $user->id,
                'target_id'   => $target->getKey(),
                'target_type' => $target->getMorphClass(),
            ],
            ['value' => $value]
        );
    }
}