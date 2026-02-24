<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Enums\ReportReason;
use App\Enums\NotificationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportModerationService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(Report $report): void
    {
        DB::transaction(function () use ($report) {
            $target = $report->target_type::withoutGlobalScopes()
                ->when(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses($report->target_type)), function ($query) {
                    return $query->withTrashed();
                })
                ->find($report->target_id);

            if (!$target) return;

            $wasActionTaken = false;

            // If the target is user, we permanently delete it (Hard Delete).
            if ($target instanceof User) {
                $target->delete(); // This will delete the user and set user_id = null in their posts.
                $wasActionTaken = true; 
            } else {
                // For posts and comments
                $wasActionTaken = match ($report->reason_type) {
                    ReportReason::SPAM => $this->handleSpam($target),
                    default => $this->hideContent($target),
                };
            }

            if ($wasActionTaken) {
                $this->applyReputationChanges($report, $target);
            }
        });
    }

    protected function applyReputationChanges(Report $report, $target): void
    {
        // If target is User, author is the target. If Post/Comment, author is target->user.
        $author = ($target instanceof User) ? $target : ($target->user ?? null);
        $reporter = $report->reporter;

        if ($author) {
            $penalty = 10;
            $author->decrement('reputation_points', $penalty);
            
            $this->notificationService->notify(
                recipient: $author,
                type: NotificationType::CONTENT_REMOVED,
                target: $target,
                message: "A report against your content was resolved. You lost {$penalty} reputation points."
            );
        }

        if ($reporter) {
            $reward = 2;
            $reporter->increment('reputation_points', $reward);

            $this->notificationService->notify(
                recipient: $reporter,
                type: NotificationType::REPORT_RESOLVED,
                message: "Your report was resolved. We've taken action and rewarded you {$reward} reputation points."
            );
        }
    }

    protected function hideContent($model): bool
    {
        if (!$model || $model instanceof User) return false;

        // Cleanup Best Answer if a comment is being hidden
        if ($model instanceof \App\Models\Comment) {
            $model->post()->where('best_comment_id', $model->id)
                  ->update(['best_comment_id' => null]);
        }

        // Force the save to database
        return $model->forceFill(['is_hidden' => true])->save();
    }

    protected function handleSpam($target): bool
    {
        // Only auto-hide if the model has a spam score mechanism
        if (method_exists($target, 'increaseSpamScore')) {
            $target->increaseSpamScore();
            $threshold = config('moderation.spam_threshold', 5);
            
            if ($target->spam_score >= $threshold) {
                return $this->hideContent($target);
            }
            return true; 
        } 
        return $this->hideContent($target);
    }
}