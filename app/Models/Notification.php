<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Notification extends Model
{
    
    use HasFactory;

    // ------------------------------------------------------------------
    // Mass Assignable Attributes
    // ------------------------------------------------------------------
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',             // Recipient of the notification
        'actor_id',            // The user who triggered the action (optional)
        'related_content_id',  // ID of the related content (post/comment/etc.)
        'related_content_type', // Model type for polymorphic relation
        'type',                // Notification type (comment, vote, follow, system)
        'message',             // Notification message text
        'is_read',             // Read status (true/false)
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Get the user who received this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who triggered this notification (actor).
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Get the related content (e.g., post, comment) for this notification.
     */
    public function relatedContent(): MorphTo
    {
        return $this->morphTo();
    }
}
