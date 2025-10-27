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
        'user_id',             // Recipient
        'actor_id',            // The user who triggered this notification
        'related_content_id',  // ID of related entity
        'related_content_type',// Polymorphic type (post/comment/etc.)
        'type',                // Notification type
        'message',             // Notification text
        'is_read',             // Read/unread flag
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function relatedContent(): MorphTo
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
        $this->update(['is_read' => true]);
    }
}
