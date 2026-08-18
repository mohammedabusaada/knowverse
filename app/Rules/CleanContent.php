<?php

namespace App\Rules;

use App\Services\ContentModerationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Data Integrity Guard: Lexical Analysis Rule.
 * Screens user input for prohibited language (always) and excessive external links
 * (anti-spam friction that trusted, high-reputation scholars are exempt from).
 */
class CleanContent implements ValidationRule
{
    protected ContentModerationService $moderationService;

    public function __construct()
    {
        $this->moderationService = new ContentModerationService;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $text = (string) $value;

        // 1. Profanity / hate / spam wording is rejected for every user.
        if ($this->moderationService->containsBlockedWords($text)) {
            $fail("The {$attribute} contains prohibited or inappropriate language.");

            return;
        }

        // 2. The external-link limit is anti-spam friction. Trusted (high-reputation)
        //    scholars bypass it; everyone else is held to the configured limit.
        $user = Auth::user();
        $isTrusted = $user && $user->hasPrivilege('trusted');

        if (! $isTrusted && $this->moderationService->hasExcessiveLinks($text)) {
            $fail("The {$attribute} contains too many external links.");
        }
    }
}
