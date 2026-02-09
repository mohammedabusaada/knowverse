<?php

namespace App\Services;

class ContentFilter
{
    protected array $blacklist;

    public function __construct()
    {
        $this->blacklist = config('content_filter.blocked_words', []);
    }

    /**
     * Validates content and returns an error message if invalid.
     */
    public function getValidationError(string $text): ?string
    {
        // 1. Check for Blacklisted Words
        foreach ($this->blacklist as $word) {
            if (stripos($text, trim($word)) !== false) {
                return 'Your content contains words that violate our community guidelines.';
            }
        }

        // 2. Check for Excessive Links (Anti-Spam)
        $linkCount = preg_match_all('/(https?:\/\/[^\s]+|www\.[^\s]+)/i', $text);
        if ($linkCount > 3) {
            return 'Too many links! Please limit your post to a maximum of 3 URLs to prevent spam.';
        }

        return null; // All good!
    }
}