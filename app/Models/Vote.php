<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    MorphTo
};

class Vote extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
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
    // Utility
    // ------------------------------------------------------------------

    public static function toggle(User $user, Model $target, int $value): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $user->id,
                'target_id' => $target->id,
                'target_type' => get_class($target),
            ],
            ['value' => $value]
        );
    }
}
