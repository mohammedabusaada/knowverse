<?php

namespace App\Services;

/**
 * Automated Lexical Analysis and Moderation Engine.
 * Scans user-generated content against configured heuristics to enforce community guidelines preemptively.
 */
class ContentModerationService
{
/**
     * Evaluates the provided text against the application's global blocklist.
     * Uses normalization techniques to detect obfuscated profanity or banned terms.
     */
    public function containsBlockedWords(string $text): bool
    {
        $blockedWords = config('content_filter.blocked_words', []);
        
        // 1. Data Normalization: Standardize text to lowercase for accurate matching
        $text = mb_strtolower($text);

        foreach ($blockedWords as $word) {
            $word = mb_strtolower(trim($word));
            
            // 2. Pattern Matching: Detect prohibited substrings
            if (str_contains($text, $word)) {
                return true;
            }
        }

        // 3. Anti-Spam Heuristics: Flag content containing an excessive number of external links
        $linkCount = preg_match_all('/(https?:\/\/[^\s]+|www\.[^\s]+)/i', $text);
        if ($linkCount > 3) {
            return true; // Treat as potential spam/bot activity
        }

        return false;
    }
}