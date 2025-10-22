<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
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
        'user_id',         // Owner of the post
        'title',           // Title of the post
        'body',            // Main content
        'image',           // Optional image URL
        'status',          // Post status (draft, published, archived)
        'best_comment_id', // ID of the best comment (if any)
        'view_count',      // Number of views
        'upvote_count',    // Number of upvotes
        'downvote_count',  // Number of downvotes
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * The user who created this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comments associated with this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The best comment selected for this post.
     */
    public function bestComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'best_comment_id');
    }

    /**
     * Tags attached to this post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    /**
     * Votes cast on this post (polymorphic relation).
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'target');
    }

    /**
     * Reports submitted for this post (polymorphic relation).
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'target');
    }
}
