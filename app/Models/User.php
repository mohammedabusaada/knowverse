<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    BelongsToMany
};

use App\Models\NotificationPreference;

/**
 * Core User Entity
 * Handles scholar authentication, authorization, and academic reputation.
 * Integrates SoftDeletes to support reversible account deactivation. [cite: 157]
 */
class User extends Authenticatable implements AuthorizableContract, MustVerifyEmail
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
        'public_follow_lists',
        'reputation_points',
        'last_login_at',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'banned_at'         => 'datetime',
        'password'          => 'hashed',
        'reputation_points' => 'integer',
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
            ->whereNull('parent_id');
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
        return $this->belongsToMany(Post::class, 'saved_posts')
            ->withTimestamps();
    }

    public function followedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_follows')
            ->withTimestamps();
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    // ============================================
    // Accessors (Properties you can access without ())
    // ============================================

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->username;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if (
            !$this->profile_picture ||
            !Storage::disk('public')->exists($this->profile_picture)
        ) {
            return asset('images/default-avatar.png');
        }

        return Storage::disk('public')->url($this->profile_picture);
    }

    public function getJoinedDateAttribute(): string
    {
        return $this->created_at->format('F Y');
    }

    /**
     * Standardizes the retrieval of the suspension state.
     */
    public function getIsBannedAttribute(): bool
    {
        return !is_null($this->banned_at);
    }

    // ============================================
    // Utility & Permissions (Methods you MUST call with ())
    // ============================================

    public function notificationEnabled($type): bool
    {
        $typeName = $type instanceof \App\Enums\NotificationType
            ? $type->value
            : $type;

        return $this->notificationPreferences()
            ->where('type', $typeName)
            ->value('enabled') ?? true;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 2;
    }

    public function isModerator(): bool
    {
        return $this->role_id === 3;
    }

    public function canModerate(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function canSeeHiddenContent(): bool
    {
        return $this->canModerate();
    }

    public function addReputation(string $action, ?int $points = null, ?Model $source = null)
    {
        return app(\App\Services\ReputationService::class)
            ->award($this, $action, $points, $source);
    }

    public function removeReputation(string $action, ?Model $source = null)
    {
        return app(\App\Services\ReputationService::class)
            ->remove($this, $action, $source);
    }

    public function totalReputation(): int
    {
        return $this->reputation_points;
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()
            ->where('follower_id', $user->id)
            ->exists();
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    /**
     * Domain Logic: Determines the scholar's rank based on cumulative contribution points. [cite: 193]
     */
    public function getAcademicStanding(): string
    {
        $points = $this->reputation_points;

        return match (true) {
            $points >= 1000 => 'Distinguished Fellow',
            $points >= 500  => 'Senior Scholar',
            $points >= 250  => 'Associate Researcher',
            $points >= 100  => 'Active Contributor',
            $points >= 50   => 'Junior Researcher',
            default         => 'Novice Scholar',
        };
    }

    public function getStandingColor(): string
    {
        $points = $this->reputation_points;

        return match (true) {
            $points >= 500  => 'text-accent',
            $points >= 100  => 'text-ink',
            default         => 'text-muted',
        };
    }
}