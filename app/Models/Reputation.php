<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

/**
 * Reputation Audit Ledger
 * Implements an "Append-Only Ledger" pattern. Records are immutable and 
 * serve as a verifiable history of a user's academic standing. [cite: 117, 118]
 */
class Reputation extends Model
{
    use HasFactory;

    // Disables default timestamps as record updates are architecturally prohibited
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',      // e.g., 'post_upvoted', 'best_answer_received'
        'delta',       // The point change (positive or negative integer)
        'source_id',   // ID of the entity that triggered the change
        'source_type', // Type of the entity
        'note',        // Optional human-readable context
    ];

    protected $casts = [
        'delta'      => 'integer',
        'created_at' => 'datetime',
    ];

    // ==============================================================================
    // Relationships
    // ==============================================================================

    /**
     * The scholar whose reputation is affected.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The underlying entity responsible for this ledger entry.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    // ==============================================================================
    // Scopes
    // ==============================================================================

    /**
     * Retrieve the chronological reputation history for a specific scholar.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)->orderByDesc('created_at');
    }

    // ==============================================================================
    // Factory / Utility
    // ==============================================================================

    /**
     * Append a new transaction to the reputation ledger.
     */
    public static function record(int $userId, string $action, int $delta, ?Model $source = null, ?string $note = null): self
    {
        return self::create([
            'user_id'     => $userId,
            'action'      => $action,
            'delta'       => $delta,
            'source_id'   => $source?->getKey(),
            'source_type' => $source ? $source->getMorphClass() : null,
            'note'        => $note,
        ]);
    }
}