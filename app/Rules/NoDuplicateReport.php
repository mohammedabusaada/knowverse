<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Enums\ReportStatus;

class NoDuplicateReport implements ValidationRule
{
    public function __construct(
        protected ?string $targetType,
        protected int $targetId
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If targetType is null (invalid input), let the 'in:post,comment,user' validator handle it
        if (!$this->targetType) {
            return;
        }

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