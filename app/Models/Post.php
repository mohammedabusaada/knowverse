<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    MorphMany
};

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // Constants
    // ------------------------------------------------------------------
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image',
        'status',
        'best_comment_id',
        'view_count',
        'upvote_count',
        'downvote_count',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function bestComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'best_comment_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'target');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'target');
    }

    public function reputations(): MorphMany
    {
        return $this->morphMany(Reputation::class, 'source');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(UserActivity::class, 'target');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('view_count');
    }

    // ------------------------------------------------------------------
    // Utility Methods
    // ------------------------------------------------------------------

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function totalVotes(): int
    {
        return $this->upvote_count - $this->downvote_count;
    }
}
