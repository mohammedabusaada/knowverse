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
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'deleted_user',
            'full_name' => 'Deleted User',
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

    public function updateVoteCounts(): void
{
    $up = $this->votes()->where('value', 1)->count();
    $down = $this->votes()->where('value', -1)->count();

    $this->update([
        'upvote_count'   => $up,
        'downvote_count' => $down,
    ]);
}

public function increaseSpamScore(int $amount = 1): void
{
    $this->increment('spam_score', $amount);
}
public function scopeVisible($query)
{
    return $query->where('is_hidden', false);
}

protected static function booted()
{
    static::addGlobalScope('visibility', function ($builder) {
        // 1. If we are in the admin area, show everything
        if (Request::is('admin/*')) {
            return;
        }

        // 2. Handle authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            // Admins see everything everywhere
            if ($user->isAdmin()) {
                return;
            }

            // Users see public content OR their own hidden content 
            // (so they can see why it was hidden)
            $builder->where(function ($query) use ($user) {
                $query->where('is_hidden', false)
                      ->orWhere('user_id', $user->id);
            });
        } else {
            // 3. Guests only see non-hidden content
            $builder->where('is_hidden', false);
        }
    });
}

}
