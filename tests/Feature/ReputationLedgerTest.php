<?php

use App\Models\{User, Post, Vote, Reputation};

/*
|--------------------------------------------------------------------------
| Reputation Ledger — integrity & immutability
|--------------------------------------------------------------------------
| These tests validate the core scientific claim of the platform: the
| reputation ledger is APPEND-ONLY and the denormalised aggregate is always
| exactly the sum of the ledger. They also serve as the regression guard for
| the fix to ReputationService::remove().
*/

// Roles are seeded globally by Tests\TestCase::setUp(), so User::factory() works.

it('keeps reputation_points exactly equal to the ledger sum, even after a vote is retracted', function () {
    // Author starts from a clean, ledger-backed zero.
    $author = User::factory()->create(['role_id' => 1, 'reputation_points' => 0]);
    $post   = Post::factory()->create(['user_id' => $author->id]);

    // Three different scholars upvote the post.
    $voters = User::factory()->count(3)->create(['role_id' => 1]);
    foreach ($voters as $voter) {
        Vote::castVote($voter, $post, 1);
    }

    $author->refresh();
    $ledgerSum = (int) Reputation::where('user_id', $author->id)->sum('delta');

    // Invariant holds after the awards (post_created +5, three upvotes +15 = 20).
    expect((int) $author->reputation_points)->toBe($ledgerSum);

    // One scholar retracts their upvote.
    Vote::where('user_id', $voters->first()->id)
        ->where('target_id', $post->id)
        ->where('target_type', $post->getMorphClass())
        ->first()
        ->delete();

    $author->refresh();

    // INVARIANT STILL HOLDS — this is the bug that the fix resolves: previously
    // the ledger lost all three rows while the score dropped by only one upvote.
    expect((int) $author->reputation_points)
        ->toBe((int) Reputation::where('user_id', $author->id)->sum('delta'));
});

it('reverses reputation with a compensating entry instead of deleting ledger rows (append-only)', function () {
    $author = User::factory()->create(['role_id' => 1, 'reputation_points' => 0]);
    $post   = Post::factory()->create(['user_id' => $author->id]);

    $voters = User::factory()->count(3)->create(['role_id' => 1]);
    foreach ($voters as $voter) {
        Vote::castVote($voter, $post, 1);
    }

    // Retract one upvote.
    Vote::where('user_id', $voters->first()->id)->first()->delete();

    // The three ORIGINAL award entries are preserved — the ledger is immutable.
    expect(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted')->count())
        ->toBe(3);

    // A single compensating (negative) reversal entry was appended.
    expect(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted_reverted')->count())
        ->toBe(1);
});
