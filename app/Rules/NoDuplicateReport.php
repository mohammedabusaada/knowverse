<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Enums\ReportStatus;

/**
 * Queue Spam Mitigation: Idempotency Guard.
 * Ensures a scholar cannot flood the moderation dashboard by repeatedly reporting 
 * the exact same entity while it is still pending review.
 */
class NoDuplicateReport implements ValidationRule
{
    public function __construct(
        protected ?string $targetType,
        protected int $targetId
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Bypass evaluation if the target type is inherently invalid
        if (!$this->targetType) {
            return;
        }

        // Query existing active reports by the authenticated user
        $exists = Report::where('reporter_id', Auth::id())
            ->where('target_type', $this->targetType)
            ->where('target_id', $this->targetId)
            ->where('status', ReportStatus::PENDING) 
            ->exists();

        if ($exists) {
            $fail('You have already reported this content. Our moderation team is currently reviewing it.');
        }
    }
}