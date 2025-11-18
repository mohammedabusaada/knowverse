<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    MorphMany
};
use League\CommonMark\CommonMarkConverter;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
        'upvote_count',
        'downvote_count',
    ];

    protected $casts = [
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
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
    // Utility
    // ------------------------------------------------------------------

    public function totalVotes(): int
    {
        return $this->upvote_count - $this->downvote_count;
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getBodyHtmlAttribute()
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($this->body)->getContent();
    }
}
