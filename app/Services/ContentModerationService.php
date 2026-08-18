<?php

namespace App\Services;

/**
 * Automated Lexical Analysis and Moderation Engine.
 * Scans user-generated content against configured heuristics to enforce community guidelines.
 */
class ContentModerationService
{
    /**
     * Inflectional suffixes tolerated after a blocked term, so that plural and
     * conjugated forms are caught without resorting to a bare substring match.
     */
    private const INFLECTIONS = 's|es|ed|ing|er|ers';

    /**
     * Evaluates the provided text against the application's profanity / hate / spam blocklist.
     * Applies to ALL users regardless of reputation.
     *
     * Single-word entries are matched on word boundaries rather than as raw substrings.
     * A substring match rejects legitimate academic vocabulary that merely contains a
     * blocked term — "oxymoron" contains "moron", and the surname "Nazir" contains
     * "nazi" — while boundary matching still catches inflected forms such as
     * "idiots" or "fucking" via self::INFLECTIONS.
     *
     * Multi-word or punctuated entries ("whatsapp +", "telegram @") cannot rely on word
     * boundaries and are matched literally, as before.
     */
    public function containsBlockedWords(string $text): bool
    {
        $blockedWords = config('content_filter.blocked_words', []);

        // Standardize text to lowercase for accurate matching.
        $text = mb_strtolower($text);

        foreach ($blockedWords as $word) {
            $word = mb_strtolower(trim($word));

            if ($word === '') {
                continue;
            }

            // Entries made purely of letters and digits are boundary-matched.
            if (preg_match('/^[\p{L}\p{N}]+$/u', $word)) {
                $pattern = '/(?<![\p{L}\p{N}])'
                    .preg_quote($word, '/')
                    .'(?:'.self::INFLECTIONS.')?'
                    .'(?![\p{L}\p{N}])/u';

                if (preg_match($pattern, $text)) {
                    return true;
                }

                continue;
            }

            // Entries containing spaces or punctuation keep literal matching.
            if (str_contains($text, $word)) {
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
