<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    MorphMany
};
use League\CommonMark\CommonMarkConverter;

/**
 * Primary Discussion Entity
 * Manages scholarly posts and enforces global visibility constraints.
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // State Constants for Post Lifecycle
    // ------------------------------------------------------------------
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image',
        'status',
        'is_hidden',
        'best_comment_id',
        'view_count',
        'upvote_count',
        'downvote_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_count' => 'integer',
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
         * Restrict hidden content to its author and system administrators.
         */
        static::addGlobalScope('visibility', function ($builder) {
            if (app()->runningInConsole() || Request::is('admin/*')) {
                return;
            }

            if (Auth::check()) {
                $user = Auth::user();
                
                // Administrators possess master-view permissions
                if ($user->isAdmin()) {
                    return;
                }

                $builder->where(function ($query) use ($user) {
                    $query->where('posts.is_hidden', false)
                          ->orWhere('posts.user_id', $user->id);
                });
            } else {
                // Anonymous guests are restricted to public records only
                $builder->where('posts.is_hidden', false);
            }
        });

        /**
         * Scope 2: Active Author Logic
         * Hide content belonging to temporarily deactivated (soft-deleted) scholars.
         */
        static::addGlobalScope('activeAuthor', function ($builder) {
            if (app()->runningInConsole() || Request::is('admin/*')) {
                return;
            }

            $builder->where(function ($query) {
                // Include orphaned posts (where author is hard-deleted -> user_id is null)
                // OR include posts where the associated author is currently active.
                $query->whereNull('posts.user_id')
                      ->orWhereHas('user');
            });
        });
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Relationship: Defensive mapping to handle orphaned posts with a fallback UI identity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'deleted_scholar',
            'full_name' => 'Deleted Scholar',
            'profile_picture' => null,
            'role_id' => 1,
        ]);
    }

    public function comments(): HasMany
    {
        // Returns only top-level comments
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

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_posts')
                    ->withTimestamps();
    }

    // ------------------------------------------------------------------
    // Query Scopes
    // ------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('posts.status', self::STATUS_PUBLISHED);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('posts.view_count');
    }

    public function scopeVisible($query)
    {
        return $query->where('posts.is_hidden', false);
    }

    public function scopeFilterByTags($query, ?array $tagIds = null)
    {
        if (!$tagIds) {
            return $query;
        }

        return $query->whereHas('tags', function ($tagQuery) use ($tagIds) {
            $tagQuery->whereIn('tags.id', $tagIds);
        });
    }

    // ------------------------------------------------------------------
    // Utility Methods & Helpers
    // ------------------------------------------------------------------

    /**
     * Safely increment the view counter.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Calculate the net score of the post.
     */
    public function totalVotes(): int
    {
        return $this->upvote_count - $this->downvote_count;
    }

    /**
     * Accessor: Generates a full asset URL for the cover image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Accessor: Converts raw Markdown body into sanitized HTML.
     */
    public function getFormattedBodyAttribute(): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($this->body ?? '')->getContent();
    }

    /**
     * Re-calculates and persists aggregate vote counts.
     */
    public function updateVoteCounts(): void
    {
        $this->update([
            'upvote_count'   => $this->votes()->where('value', 1)->count(),
            'downvote_count' => $this->votes()->where('value', -1)->count(),
        ]);
    }

    /**
     * Checks if a specific scholar has saved this post.
     */
    public function isSavedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('savedByUsers')) {
            return $this->savedByUsers->contains('id', $user->id);
        }

        return $this->savedByUsers()->wherePivot('user_id', $user->id)->exists();
    }
}