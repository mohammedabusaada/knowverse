<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    
    use HasFactory, SoftDeletes;

    // ------------------------------------------------------------------
    // Mass Assignable Attributes
    // ------------------------------------------------------------------
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reporter_id',   // The user who submitted the report
        'target_id',     // ID of the reported entity (post/comment/etc.)
        'target_type',   // Model type of the target (for polymorphic relation)
        'reason',        // Description or reason for reporting
        'status',        // Current status: pending, reviewed, dismissed
        'reviewed_by',   // The admin/moderator who reviewed it
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Get the user who submitted the report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the reviewer (admin/moderator) who handled the report.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the reported content (polymorphic relation).
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
