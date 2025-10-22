<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // Mass Assignable Attributes
    // ------------------------------------------------------------------
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',        // The post this comment belongs to
        'user_id',        // The user who wrote the comment
        'parent_id',      // If this comment is a reply, this references the parent comment
        'body',           // The comment text
        'upvote_count',   // Optional: Number of upvotes
        'downvote_count', // Optional: Number of downvotes
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Get the post associated with this comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who authored this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (if this is a reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get all votes associated with this comment (polymorphic relation).
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'target');
    }
}
