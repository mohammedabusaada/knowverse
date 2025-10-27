<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

class Reputation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'delta',
        'source_id',
        'source_type',
        'note',
    ];

    protected $casts = [
        'delta' => 'integer',
        'created_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)->orderByDesc('created_at');
    }

    // ------------------------------------------------------------------
    // Utility
    // ------------------------------------------------------------------

    public static function record(int $userId, string $action, int $delta, ?Model $source = null, ?string $note = null): self
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'delta' => $delta,
            'source_id' => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'note' => $note,
        ]);
    }
}
