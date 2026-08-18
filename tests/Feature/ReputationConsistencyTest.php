<?php

use App\Models\{User, Post, Comment, Vote, Reputation, Report};
use App\Enums\{ReportStatus, ReportReason};
use App\Services\ReportModerationService;

/*
|--------------------------------------------------------------------------
| Reputation ledger consistency  (the platform's headline property)
|--------------------------------------------------------------------------
| INVARIANT:  for every user,  reputation_points === SUM(reputations.delta).
| These tests stress the invariant across high-volume interleaved voting,
| moderation penalties, and Author's-Pick award/retraction. The concurrent
| (parallel) version of this property is validated by the load harness in
| Phase 3, which re-checks the invariant after the vote stress test.
*/

/**
 * Assert the ledger invariant holds for every user currently in the database.
 */
function assertReputationLedgerConsistent(): void
{
    User::query()->get()->each(function (User $u) {
        $ledger = (int) Reputation::where('user_id', $u->id)->sum('delta');
        expect((int) $u->reputation_points)->toBe($ledger);
    });
}

test('the invariant holds across a high volume of interleaved votes and retractions', function () {
    $users = User::factory()->count(8)->create(['reputation_points' => 0]);
    $posts = $users->map(fn (User $u) => Post::factory()->create(['user_id' => $u->id]));

    // ~150 interleaved vote operations (cast / flip) from random scholars.
    for ($i = 0; $i < 150; $i++) {
        $voter = $users->random();
        $post = $posts->random();
        if ($voter->id === $post->user_id) {
            continue; // self-votes are no-ops by design
        }
        Vote::castVote($voter, $post, random_int(0, 1) ? 1 : -1);
    }

    // Retract ~30 random votes.
    Vote::query()->inRandomOrder()->limit(30)->get()->each->delete();

    assertReputationLedgerConsistent();
});

test('moderation penalties preserve the invariant (routed through the ledger)', function () {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]); // +5
    $reporter = User::factory()->create(['reputation_points' => 0]);
    $moderator = User::factory()->moderator()->create(['reputation_points' => 0]);

    $report = Report::create([
        'reporter_id' => $reporter->id,
        'target_type' => Post::class,
        'target_id'   => $post->id,
        'reason_type' => ReportReason::SPAM,
        'status'      => ReportStatus::PENDING,
    ]);

    $report->markAsResolved($moderator);
    app(ReportModerationService::class)->handle($report);

    assertReputationLedgerConsistent();

    // The penalty and reward are genuine ledger entries (not silent direct writes).
    expect(Reputation::where('user_id', $author->id)->where('action', 'moderation_penalty')->exists())->toBeTrue();
    expect(Reputation::where('user_id', $reporter->id)->where('action', 'report_reward')->exists())->toBeTrue();
});

test('Author\'s Pick award and retraction preserve the invariant', function () {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]);
    $commenter = User::factory()->create(['reputation_points' => 0]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $commenter->id, 'is_hidden' => false]);

    $this->actingAs($author)->post(route('comments.best', $comment))->assertRedirect();
    assertReputationLedgerConsistent();

    $this->actingAs($author)->post(route('comments.unbest', $comment))->assertRedirect();
    assertReputationLedgerConsistent();
});
