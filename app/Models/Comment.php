<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    MorphMany
};
use League\CommonMark\CommonMarkConverter;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
        'is_hidden',
        'spam_score',
        'upvote_count',
        'downvote_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'is_hidden' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model to apply global visibility and state logic.
     */
    protected static function booted()
    {
        /**
         * Scope 1: Visibility Logic
         * Restricts flagged or hidden content to its owner and system administrators.
         */
        static::addGlobalScope('visibility', function ($builder) {
            // Bypass filtering for administrative routes
            if (Request::is('admin/*')) {
                return;
            }

            if (Auth::check()) {
                $user = Auth::user();

                // Administrators possess master-view access
                if ($user->isAdmin()) {
                    return;
                }

                $builder->where(function ($query) use ($user) {
                    $query->where('comments.is_hidden', false)
                          ->orWhere('comments.user_id', $user->id);
                });
            } else {
                // Anonymous guests see public content only
                $builder->where('comments.is_hidden', false);
            }
        });

        /**
         * Scope 2: Active Author Logic
         * Hides responses belonging to temporarily deactivated (soft-deleted) scholars.
         */
        static::addGlobalScope('activeAuthor', function ($builder) {
            if (Request::is('admin/*')) {
                return;
            }

            $builder->where(function ($query) {
                // Include orphaned comments (author is permanently deleted -> user_id is null)
                // OR include comments where the associated author is currently active.
                $query->whereNull('comments.user_id')
                      ->orWhereHas('user');
            });
        });
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'deleted_scholar',
            'full_name' => 'Deleted Scholar',
            'profile_picture' => null,
            'role_id' => 1,
        ]);
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
    // Query Scopes
    // ------------------------------------------------------------------

    public function scopeVisible($query)
    {
        return $query->where('comments.is_hidden', false);
    }

    // ------------------------------------------------------------------
    // Utility & Helper Methods
    // ------------------------------------------------------------------

    /**
     * Calculate the net vote score of the comment.
     */
    public function totalVotes(): int
    {
        return $this->upvote_count - $this->downvote_count;
    }

    /**
     * Determine if the current comment is a reply to another comment.
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Accessor: Converts Markdown body into sanitized HTML for display.
     */
    public function getBodyHtmlAttribute(): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($this->body ?? '')->getContent();
    }

    /**
     * Synchronizes aggregate vote counts for performance optimization.
     */
    public function updateVoteCounts(): void
    {
        $this->update([
            'upvote_count'   => $this->votes()->where('value', 1)->count(),
            'downvote_count' => $this->votes()->where('value', -1)->count(),
        ]);
    }

    /**
     * Increment the spam counter for moderation purposes.
     */
    public function increaseSpamScore(int $amount = 1): void
    {
        $this->increment('spam_score', $amount);
    }
}