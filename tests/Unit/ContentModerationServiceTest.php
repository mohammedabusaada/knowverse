<?php

use App\Services\ContentModerationService;

/*
 * Pure unit coverage for the moderation primitives. These methods take a string and
 * return a boolean with no database or authentication involved, so they are exercised
 * directly here rather than only through the HTTP layer. Testing them in isolation is
 * what surfaces false positives, which a request-level test never reaches: it only ever
 * submits content that is genuinely in violation.
 */

beforeEach(function () {
    $this->moderation = new ContentModerationService;
});

describe('containsBlockedWords', function () {
    it('rejects a blocked term used on its own', function (string $text) {
        expect($this->moderation->containsBlockedWords($text))->toBeTrue();
    })->with([
        'You are an idiot.',
        'What a moron.',
        'nazi propaganda',
    ]);

    it('is case insensitive', function () {
        expect($this->moderation->containsBlockedWords('You are an IDIOT.'))->toBeTrue()
            ->and($this->moderation->containsBlockedWords('YoU aRe An IdIoT.'))->toBeTrue();
    });

    it('still catches inflected and pluralised forms', function (string $text) {
        expect($this->moderation->containsBlockedWords($text))->toBeTrue();
    })->with([
        'They are all idiots.',
        'What a bunch of morons.',
        'This fucking thing.',
        'Reading nazis in context.',
    ]);

    /*
     * The regression this suite exists for. A substring matcher rejects each of these:
     * "oxymoron" contains "moron" and "Nazir" contains "nazi". On an academic platform
     * that silently blocks a rhetorical term and a common surname.
     */
    it('does not reject legitimate vocabulary that merely contains a blocked term', function (string $text) {
        expect($this->moderation->containsBlockedWords($text))->toBeFalse();
    })->with([
        'That statement is an oxymoron.',
        'Dr. Nazir published the paper.',
        'Nazira Abu Amra co-authored the study.',
        'A study of shitake mushroom cultivation.',
        'The assignment was submitted on time.',
    ]);

    it('matches multi-word and punctuated entries literally', function (string $text) {
        expect($this->moderation->containsBlockedWords($text))->toBeTrue();
    })->with([
        'contact me on whatsapp + 123',
        'get free crypto today',
        'reach me at telegram @handle',
    ]);

    it('accepts ordinary academic prose', function (string $text) {
        expect($this->moderation->containsBlockedWords($text))->toBeFalse();
    })->with([
        'A comparative analysis of distributed consensus protocols.',
        'The reputation ledger guarantees append-only semantics.',
        '',
    ]);
});

describe('hasExcessiveLinks', function () {
    $link = fn (int $n) => implode(' ', array_map(
        fn ($i) => "https://example.com/paper-$i",
        range(1, max(0, $n))
    ));

    it('permits a submission at exactly the configured limit', function () use ($link) {
        $max = config('content_filter.max_links');

        expect($this->moderation->hasExcessiveLinks($link($max)))->toBeFalse();
    });

    it('rejects a submission one link over the configured limit', function () use ($link) {
        $max = config('content_filter.max_links');

        expect($this->moderation->hasExcessiveLinks($link($max + 1)))->toBeTrue();
    });

    it('permits text containing no links at all', function () {
        expect($this->moderation->hasExcessiveLinks('No references cited here.'))->toBeFalse();
    });

    it('honours an explicitly supplied limit over the configured one', function () use ($link) {
        expect($this->moderation->hasExcessiveLinks($link(2), 1))->toBeTrue()
            ->and($this->moderation->hasExcessiveLinks($link(2), 5))->toBeFalse();
    });

    it('counts bare www links as well as scheme-qualified ones', function () {
        $text = 'www.a.com www.b.com www.c.com www.d.com';

        expect($this->moderation->hasExcessiveLinks($text))->toBeTrue();
    });
});
