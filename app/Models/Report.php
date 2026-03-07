<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use App\Enums\ReportStatus;
use App\Enums\ReportReason;

/**
 * Moderation Report Entity
 * Facilitates the reporting and resolution lifecycle of content violations.
 */
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
        'resolved_by',
        'resolved_at',
    ];

    /**
     * Cast attributes to Enums and specific data types to guarantee type safety throughout the application.
     */
    protected $casts = [
        'status'      => ReportStatus::class,
        'reason_type' => ReportReason::class,
        'resolved_at' => 'datetime',
    ];

    /**
     * Default model attributes. Every new report requires moderation by default.
     */
    protected $attributes = [
        'status' => ReportStatus::PENDING,
    ];

    // ==============================================================================
    // Relationships
    // ==============================================================================

    /**
     * The user who submitted the report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * The moderator or administrator who finalized the report.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

/**
     * Polymorphic Target Resolution
     * CRITICAL: Bypasses visibility filters to ensure moderators can inspect 
     * deleted or hidden content that has been flagged. 
     */
    public function target(): MorphTo
    {
        return $this->morphTo()
            ->withoutGlobalScopes()
            ->withTrashed();
    }

    // ==============================================================================
    // Query Scopes
    // ==============================================================================

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

    // ==============================================================================
    // State Transitions
    // ==============================================================================

    /**
     * Transitions the report to a resolved state and logs the acting admin.
     */
    public function markAsResolved(User $admin): void
    {
        $this->update([
            'status'      => ReportStatus::RESOLVED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Transitions the report to a dismissed state and logs the acting admin.
     */
    public function markAsDismissed(User $admin): void
    {
        $this->update([
            'status'      => ReportStatus::DISMISSED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);
    }
}