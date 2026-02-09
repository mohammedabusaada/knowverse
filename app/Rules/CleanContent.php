<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\ContentModerationService;

class CleanContent implements ValidationRule
{
    protected $moderationService;

    public function __construct()
    {
        $this->moderationService = new ContentModerationService();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->moderationService->containsBlockedWords($value)) {
            $fail("The {$attribute} contains inappropriate language.");
        }
    }
}