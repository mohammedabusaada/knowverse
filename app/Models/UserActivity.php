<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

class UserActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'target_id',
        'target_type',
        'details',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    // ------------------------------------------------------------------
// Feed Scopes
// ------------------------------------------------------------------

public function scopeForUser($query, User $user)
{
    return $query->where('user_id', $user->id);
}

public function scopeFeed($query)
{
    return $query->orderByDesc('created_at');
}

public function scopeWithTargets($query)
{
    return $query->with([
        'target' => function ($morph) {
            $morph->morphWith([
                \App\Models\Post::class => ['user', 'tags'],
                \App\Models\Comment::class => ['user', 'post'],
            ]);
        }
    ]);
}


    // ------------------------------------------------------------------
    // Utility
    // ------------------------------------------------------------------

    public static function log(User $user, string $action, ?Model $target = null, ?string $details = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'target_id' => $target?->id,
            'target_type' => $target ? get_class($target) : null,
            'details' => $details,
        ]);
    }

    public function isPublic(): bool
{
    return \App\Support\ActivityVisibility::for($this->action) === 'public';
}
}
