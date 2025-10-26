<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reputation extends Model
{
    use HasFactory;

    public $timestamps = false;

    // ------------------------------------------------------------------
    // Mass Assignable Attributes
    // ------------------------------------------------------------------
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',      // The user who earned or lost reputation
        'action',       // The action that caused the change
        'delta',        // The reputation points gained/lost
        'source_id',    // The related model ID (post/comment/etc.)
        'source_type',  // The related model type
        'note',         // Optional note or explanation
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Get the user associated with this reputation record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source (post, comment, etc.) that caused the reputation change.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
