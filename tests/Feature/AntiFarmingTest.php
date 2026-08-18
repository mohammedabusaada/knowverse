<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;

/*
|--------------------------------------------------------------------------
| Anti-farming — reputation integrity guarantees
|--------------------------------------------------------------------------
| These are the safeguards that make the reputation a credible signal:
| no self-voting, no double-counting, no farming via toggling, and no
| self-awarded "Author's Pick".
*/

test('a scholar cannot vote on their own contribution (HTTP guard)', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($author)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => 1])
        ->assertStatus(403);
});

test('self-votes never award reputation (observer guard)', function () {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]); // +5 post_created

    Vote::castVote($author, $post, 1); // self-upvote must be ignored

    expect($author->refresh()->reputation_points)->toBe(5);
});

test('a user cannot double-count votes on the same target', function () {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();

    Vote::castVote($voter, $post, 1);
    Vote::castVote($voter, $post, 1); // repeat — an upsert, not a second row

    expect(Vote::where('user_id', $voter->id)->where('target_id', $post->id)->where('target_type', 'post')->count())
        ->toBe(1);

    // post_created (5) + exactly ONE upvote (5)
    expect($author->refresh()->reputation_points)->toBe(10);
});

test('flipping a vote cannot farm reputation', function () {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]); // +5
    $voter = User::factory()->create();

    Vote::castVote($voter, $post, 1);  // +5  => 10
    Vote::castVote($voter, $post, -1); // undo +5, apply -2 => 3
    Vote::castVote($voter, $post, 1);  // undo -2, apply +5 => 10

    $author->refresh();
    expect($author->reputation_points)->toBe(10);
    // ledger invariant preserved throughout
    expect($author->reputation_points)
        ->toBe((int) Reputation::where('user_id', $author->id)->sum('delta'));
});

test('a discussion owner cannot award Author\'s Pick to their own comment', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $ownComment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $author->id]);

    $this->actingAs($author)
        ->post(route('comments.best', $ownComment))
        ->assertForbidden();
});

test('banned users cannot vote', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $banned = User::factory()->create(['banned_at' => now()]);

    $this->actingAs($banned)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => 1])
        ->assertRedirect(route('login')); // CheckBanned terminates the session
});

test('changing an upvote to a downvote reverses the upvote, then applies the downvote', function () {
    // Mirrors the exact scenario: a score is raised by an upvote, then the same
    // voter flips it to a downvote. The ledger first cancels the upvote, then
    // applies the downvote penalty — never double-counting.
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]); // post_created (+5) => 5
    $voter = User::factory()->create();

    Vote::castVote($voter, $post, 1);                 // + post_upvoted (+5)  => 10
    expect($author->fresh()->reputation_points)->toBe(10);

    Vote::castVote($voter, $post, -1);                // undo +5, then apply -2 => 3
    $author->refresh();
    expect($author->reputation_points)->toBe(3);

    // The ledger keeps the original award, appends a compensating reversal of it,
    // and records the downvote — a fully auditable trail of the flip.
    expect(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted')->exists())->toBeTrue();
    expect(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted_reverted')->exists())->toBeTrue();
    expect(Reputation::where('user_id', $author->id)->where('action', 'post_downvoted')->exists())->toBeTrue();

    // Invariant preserved throughout: cached score == sum of the ledger.
    expect($author->reputation_points)->toBe((int) Reputation::where('user_id', $author->id)->sum('delta'));
});
