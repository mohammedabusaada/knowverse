<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

class Notification extends Model
{
    use HasFactory;

    // ------------------------------------------------------------------
    // Constants
    // ------------------------------------------------------------------
    public const TYPE_COMMENT = 'comment';
    public const TYPE_VOTE = 'vote';
    public const TYPE_FOLLOW = 'follow';
    public const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'message',
        'is_read',
        'read_at',
        'target_id',
        'target_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ------------------------------------------------------------------
    // Utility
    // ------------------------------------------------------------------

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
