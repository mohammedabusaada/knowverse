<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Reputation Audit Ledger
 * Implements an "Append-Only Ledger" pattern. Entries are never updated or deleted in
 * normal operation; a reversal is recorded as a compensating entry that references the
 * entry it cancels, giving a verifiable history of a user's academic standing.
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
        'reverses_id', // For a compensating entry: the ledger entry it reverses
        'source_id',   // ID of the entity that triggered the change
        'source_type', // Type of the entity
        'note',        // Optional human-readable context
    ];

    protected $casts = [
        'delta' => 'integer',
        'reverses_id' => 'integer',
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

    /**
     * For a compensating entry: the original ledger entry it reverses.
     */
    public function reverses(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reverses_id');
    }

    /**
     * The compensating entry that reversed this one, if any. At most one can exist.
     */
    public function reversal(): HasOne
    {
        return $this->hasOne(self::class, 'reverses_id');
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
    public static function record(int $userId, string $action, int $delta, ?Model $source = null, ?string $note = null, ?int $reversesId = null): self
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'delta' => $delta,
            'reverses_id' => $reversesId,
            'source_id' => $source?->getKey(),
            'source_type' => $source ? $source->getMorphClass() : null,
            'note' => $note,
        ]);
    }
}
