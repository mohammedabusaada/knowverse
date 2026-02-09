<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use App\Enums\ReportStatus;
use App\Enums\ReportReason;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reporter_id',
        'target_id',
        'target_type',
        'reason_type',
        'reason',
        'status',
        'resolved_by', // Standardized naming
        'resolved_at',
    ];

    protected $casts = [
        'status' => ReportStatus::class,
        'reason_type' => ReportReason::class,
        'resolved_at' => 'datetime',
    ];

    /**
     * Set default attribute values.
     */
    protected $attributes = [
        'status' => ReportStatus::PENDING,
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * The admin who resolved or dismissed this report.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the reported model (Post, Comment, etc.). 
     * Bypasses global scopes so admins can view hidden/deleted content.
     */
    public function target(): MorphTo
    {
        return $this->morphTo()
            ->withoutGlobalScopes()
            ->withTrashed();
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', ReportStatus::PENDING);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', ReportStatus::RESOLVED);
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', ReportStatus::DISMISSED);
    }

    // ------------------------------------------------------------------
    // Utility Methods
    // ------------------------------------------------------------------

    /**
     * Mark the report as resolved and record the admin responsible.
     */
    public function markAsResolved(User $admin): void
    {
        $this->update([
            'status' => ReportStatus::RESOLVED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Mark the report as dismissed.
     */
    public function markAsDismissed(User $admin): void
    {
        $this->update([
            'status' => ReportStatus::DISMISSED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }
}