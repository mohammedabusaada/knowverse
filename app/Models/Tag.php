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

     // AUTO GENERATE SLUG ON CREATE/UPDATE
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

     // RELATIONSHIP: POSTS
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }

    // RELATIONSHIP: FOLLOWERS (users who follow this tag)
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tag_follows');
    }

    // SCOPE: POPULAR TAGS
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('posts')
                     ->orderByDesc('posts_count')
                     ->limit($limit);
    }

    // REQUIRED SCOPE FOR FILTERING POSTS BY TAGS
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
