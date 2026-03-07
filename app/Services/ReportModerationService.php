<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Enums\ReportReason;
use App\Enums\NotificationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * Moderation Execution Engine.
 * Processes verified reports and applies administrative penalties strictly within 
 * atomic Database Transactions to guarantee system-wide data integrity.
 */
class ReportModerationService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Resolves an approved report and routes the corresponding penalty logic.
     */
    public function handle(Report $report): void
    {
        // Execute inside an Atomic Transaction (All operations succeed, or none do)
        DB::transaction(function () use ($report) {
            // Extract the polymorphic target, bypassing global visibility scopes 
            // to allow administration of already hidden or deleted items.
            $target = $report->target_type::withoutGlobalScopes()
                ->when(in_array(SoftDeletes::class, class_uses($report->target_type)), function ($query) {
                    return $query->withTrashed();
                })
                ->find($report->target_id);

            if (!$target) return;

            $wasActionTaken = false;

            // Phase 1: Identity Penalties (Suspend user accounts)
            if ($target instanceof User) {
                $target->update(['banned_at' => now()]); 
                $wasActionTaken = true; 
            } else {
                // Phase 2: Content Penalties (Hide discussions or responses)
                $wasActionTaken = match ($report->reason_type) {
                    ReportReason::SPAM => $this->handleSpam($target),
                    default => $this->hideContent($target),
                };
            }

            // Phase 3: Economic Impact & Auditing
            if ($wasActionTaken) {
                $this->applyReputationChanges($report, $target);
            }
        });
    }

    /**
     * Executes reputation adjustments for both the penalized author and the reporting whistleblower.
     */
    protected function applyReputationChanges(Report $report, $target): void
    {
        // Polymorphic resolution to identify the content's origin author.
        $author = ($target instanceof User) ? $target : ($target->user ?? null);
        $reporter = $report->reporter;

        // Apply a strict penalty to the violator
        if ($author) {
            $penalty = 10;
            $author->decrement('reputation_points', $penalty);

            $message = ($target instanceof User) 
                ? "Your account has been suspended and you lost {$penalty} reputation points due to community guidelines violations."
                : "A report against your content was resolved. You lost {$penalty} reputation points.";

            // Safe Notification Dispatch (Prevents crashing if WebSockets/Reverb is down)
            try {
                $this->notificationService->notify(
                    recipient: $author,
                    type: NotificationType::CONTENT_REMOVED,
                    target: $target,
                    message: $message
                );
            } catch (\Exception $e) {
                Log::warning('Moderation Notification Failed (Reverb/Pusher is likely offline): ' . $e->getMessage());
            }
        }

        // Reward the whistleblower to incentivize community moderation
        if ($reporter) {
            $reward = 2;
            $reporter->increment('reputation_points', $reward);

            try {
                $this->notificationService->notify(
                    recipient: $reporter,
                    type: NotificationType::REPORT_RESOLVED,
                    message: "Your report was resolved. We've taken action and rewarded you {$reward} reputation points."
                );
            } catch (\Exception $e) {
                Log::warning('Moderation Reward Notification Failed: ' . $e->getMessage());
            }
        }
    }

/**
     * Conceals content from the public feed while preserving database records for audit purposes.
     */
    protected function hideContent($model): bool
    {
        if (!$model || $model instanceof User) return false;

        // Referential Integrity: Nullify 'Author Pick' status if the hidden item was a selected response
        if ($model instanceof \App\Models\Comment) {
            $model->post()->where('best_comment_id', $model->id)
                  ->update(['best_comment_id' => null]);
        }

        // Forcefully update state while bypassing eloquent Observers/Scopes
        return $model->forceFill(['is_hidden' => true])->save();
    }

/**
     * Moderation logic tailored for spam thresholds.
     */
    protected function handleSpam($target): bool
    {
        if (method_exists($target, 'increaseSpamScore')) {
            $target->increaseSpamScore();
            $threshold = config('moderation.spam_threshold', 5);
            
            // Auto-conceal content only if it repeatedly exceeds algorithmic limits
            if ($target->spam_score >= $threshold) {
                return $this->hideContent($target);
            }
            return true;
        } 
        
        // Fallback for models without spam tracking
        return $this->hideContent($target);
    }
}