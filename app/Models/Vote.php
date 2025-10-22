<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
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
        'user_id',      // The user who cast the vote
        'target_id',    // ID of the target model (post/comment/etc.)
        'target_type',  // Type of the target model (for polymorphic relation)
        'value',        // Vote value (+1 or -1)
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Get the user who cast this vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the target content that was voted on.
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
