<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
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
        'name',  // The tag name (e.g., "AI", "Web Security")
        'slug',  // The unique slug for the tag (used in URLs)
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------
    /**
     * Get all posts associated with this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
}
