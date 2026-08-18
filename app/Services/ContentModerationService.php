<?php

namespace App\Services;

/**
 * Automated Lexical Analysis and Moderation Engine.
 * Scans user-generated content against configured heuristics to enforce community guidelines.
 */
class ContentModerationService
{
    /**
     * Evaluates the provided text against the application's profanity / hate / spam blocklist.
     * Applies to ALL users regardless of reputation.
     */
    public function containsBlockedWords(string $text): bool
    {
        $blockedWords = config('content_filter.blocked_words', []);

        // Standardize text to lowercase for accurate matching.
        $text = mb_strtolower($text);

        foreach ($blockedWords as $word) {
            $word = mb_strtolower(trim($word));

            if ($word !== '' && str_contains($text, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Anti-spam heuristic: whether the text contains more external links than allowed.
     * This is friction that trusted (high-reputation) users may bypass at the call site.
     */
    public function hasExcessiveLinks(string $text, ?int $max = null): bool
    {
        $max = $max ?? (int) config('content_filter.max_links', 3);

        $linkCount = preg_match_all('/(https?:\/\/[^\s]+|www\.[^\s]+)/i', $text);

        return $linkCount > $max;
    }
}
