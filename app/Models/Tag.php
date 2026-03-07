<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Bootstrap model events to maintain SEO-friendly URL slugs automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });

        static::updating(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }

    // ==============================================================================
    // Relationships
    // ==============================================================================

    /**
     * Relationship: Associated discussions within this topic.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }

    /**
     * Relationship: Scholars who have subscribed to updates for this topic.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tag_follows');
    }

    // ==============================================================================
    // Scopes & Discovery
    // ==============================================================================

    /**
     * Filters tags based on activity volume (Post count).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('posts')
                     ->orderByDesc('posts_count')
                     ->limit($limit);
    }

    /**
     * Utility scope to filter a result set based on a specific array of tag IDs.
     */
    public function scopeFilterByTags($query, ?array $tagIds = null)
    {
        if (!$tagIds) {
            return $query;
        }

        return $query->whereHas('posts', function ($postQuery) use ($tagIds) {
            $postQuery->whereHas('tags', function ($tagQuery) use ($tagIds) {
                $tagQuery->whereIn('tags.id', $tagIds);
            });
        });
    }
}