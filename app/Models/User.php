<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Models\NotificationPreference;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    BelongsToMany
};

class User extends Authenticatable implements AuthorizableContract
{
    use Authorizable, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'full_name',
        'academic_title',
        'password',
        'role_id',
        'bio',
        'profile_picture',
        'reputation_points',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
        'reputation_points' => 'integer',
        'deleted_at'        => 'datetime',
    ];

    // ============================================
    // Relationships
    // ============================================

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function parentComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id'); // count only top-level comments
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function reputations(): HasMany
    {
        return $this->hasMany(Reputation::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'saved_posts')->withTimestamps();
    }

    public function followedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_follows')->withTimestamps();
    }

    // ============================================
    // Accessors
    // ============================================

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->username;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if (!$this->profile_picture) {
            return asset('images/default-avatar.png');
        }

        if (!file_exists(public_path('storage/' . $this->profile_picture))) {
            return asset('images/default-avatar.png');
        }

        return asset('storage/' . $this->profile_picture);
    }

    public function getJoinedDateAttribute(): string
    {
        return $this->created_at->format('F Y');
    }

    // ============================================
    // Utility
    // ============================================
    public function notificationEnabled($type): bool
    {
        // If an Enum is passed, get the string value; otherwise use as is
        $typeName = $type instanceof \App\Enums\NotificationType ? $type->value : $type;

        return $this->notificationPreferences()
            ->where('type', $typeName)
            ->value('enabled') ?? true;
    }




    public function isAdmin(): bool
    {
        return optional($this->role)->name === 'admin';
    }

    public function addReputation(string $action, ?int $points = null, ?Model $source = null)
    {
        return app(\App\Services\ReputationService::class)
            ->award($this, $action, $points, $source);
    }

    public function totalReputation(): int
    {
        return $this->reputation_points;
    }

    public function removeReputation(string $action, ?Model $source = null)
    {
        return app(\App\Services\ReputationService::class)
            ->remove($this, $action, $source);
    }


    public function getRouteKeyName()
    {
        return 'username';
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }
}
