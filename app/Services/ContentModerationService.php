<?php

namespace App\Services;

class ContentModerationService
{
    public function containsBlockedWords(string $text): bool
    {
        $blockedWords = config('content_filter.blocked_words', []);
        $text = mb_strtolower($text);

        foreach ($blockedWords as $word) {
            if (str_contains($text, mb_strtolower($word))) {
                return true;
            }
        }

        return false;
    }
}