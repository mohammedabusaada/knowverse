<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
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
        'user_id',       // The user who performed the action
        'action',        // Type of action (post, comment, vote, follow, etc.)
        'target_id',     // The ID of the related record (optional)
        'target_type',   // The type of the related model (Post, Comment, etc.)
        'details',       // Additional details about the activity (optional)
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------
    /**
     * Get the user associated with this activity.
     *
     * Defines an inverse one-to-many (belongsTo) relationship
     * with the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
