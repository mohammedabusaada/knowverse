<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;
use App\Services\ReputationService;
use Illuminate\Database\UniqueConstraintViolationException;

/*
|--------------------------------------------------------------------------
| Reputation reversal - idempotency and anti-farming guarantees
|--------------------------------------------------------------------------
| Every compensating entry references the ledger entry it cancels, and a
| unique index on that reference means no entry can be reversed twice.
| These tests pin that guarantee at the service and database layers, on
| every path that reverses reputation, and close the Author's Pick
| switching loophole.
*/

$assertInvariant = function (User ...$users) {
    foreach ($users as $user) {
        expect($user->fresh()->reputation_points)
            ->toBe((int) Reputation::where('user_id', $user->id)->sum('delta'));
    }
};

it('treats a repeated reversal of the same entry as a no-op', function () use ($assertInvariant) {
    $service = app(ReputationService::class);
    $user = User::factory()->create(['reputation_points' => 0]);
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id, 'is_hidden' => false]);

    $original = $service->award($user, 'post_upvoted', null, $post);
    $service->remove($user, 'post_upvoted', $post);
    $service->remove($user, 'post_upvoted', $post); // repeated

    expect($user->fresh()->reputation_points)->toBe(0);

    $reversals = Reputation::where('user_id', $user->id)->where('action', 'post_upvoted_reverted')->get();
    expect($reversals)->toHaveCount(1)
        ->and($reversals->first()->reverses_id)->toBe($original->id);

    $assertInvariant($user);
});

it('reverses a distinct entry each time when several identical awards exist', function () use ($assertInvariant) {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id, 'is_hidden' => false]); // +5 post_created
    $voters = User::factory()->count(3)->create();

    foreach ($voters as $voter) {
        Vote::castVote($voter, $post, 1);
    }
    foreach ($voters as $voter) {
        Vote::where('user_id', $voter->id)->where('target_id', $post->id)->where('target_type', 'post')->first()->delete();
    }

    // A further reversal has nothing left to cancel.
    app(ReputationService::class)->remove($author, 'post_upvoted', $post);

    expect($author->fresh()->reputation_points)->toBe(5);

    $originalIds = Reputation::where('user_id', $author->id)->where('action', 'post_upvoted')->pluck('id')->sort()->values();
    $reversedIds = Reputation::where('user_id', $author->id)->where('action', 'post_upvoted_reverted')->pluck('reverses_id')->sort()->values();
    expect($originalIds)->toHaveCount(3)
        ->and($reversedIds->all())->toBe($originalIds->all());

    $assertInvariant($author);
});

it('refuses a second reversal of the same entry at the database layer', function () {
    $user = User::factory()->create(['reputation_points' => 0]);
    $original = app(ReputationService::class)->award($user, 'comment_created');

    Reputation::record($user->id, 'comment_created_reverted', -$original->delta, null, 'first reversal', $original->id);

    expect(fn () => Reputation::record($user->id, 'comment_created_reverted', -$original->delta, null, 'second reversal', $original->id))
        ->toThrow(UniqueConstraintViolationException::class);
});

it('reverses Author\'s Pick exactly once when a retracted pick is later deleted', function () use ($assertInvariant) {
    $owner = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $commenter = User::factory()->create(['reputation_points' => 0]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $commenter->id]);

    $this->actingAs($owner)->post(route('comments.best', $comment))->assertRedirect();
    $this->actingAs($owner)->post(route('comments.unbest', $comment))->assertRedirect();
    $comment->delete();

    expect(Reputation::where('user_id', $commenter->id)->where('action', 'authors_pick_received_reverted')->count())->toBe(1)
        ->and(Reputation::where('user_id', $owner->id)->where('action', 'authors_pick_awarded_reverted')->count())->toBe(1)
        ->and($commenter->fresh()->reputation_points)->toBe(0); // +2 comment and +15 pick, both reversed

    $assertInvariant($owner, $commenter);
});

it('reverses Author\'s Pick exactly once when its discussion and then the answer are deleted', function () use ($assertInvariant) {
    $owner = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $commenter = User::factory()->create(['reputation_points' => 0]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $commenter->id]);

    $this->actingAs($owner)->post(route('comments.best', $comment))->assertRedirect();
    $post->fresh()->delete(); // fresh(): the pick was made over HTTP, so this instance is stale
    $comment->fresh()?->delete();

    expect(Reputation::where('user_id', $commenter->id)->where('action', 'authors_pick_received_reverted')->count())->toBe(1)
        ->and(Reputation::where('user_id', $owner->id)->where('action', 'authors_pick_awarded_reverted')->count())->toBe(1);

    $assertInvariant($owner, $commenter);
});

it('never accumulates reputation when Author\'s Pick is switched between comments', function () use ($assertInvariant) {
    $owner = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $a = User::factory()->create(['reputation_points' => 0]);
    $b = User::factory()->create(['reputation_points' => 0]);
    $commentA = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $a->id]);
    $commentB = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $b->id]);

    [$a0, $b0, $o0] = [$a->fresh()->reputation_points, $b->fresh()->reputation_points, $owner->fresh()->reputation_points];

    foreach ([$commentA, $commentB, $commentA, $commentB, $commentA] as $pick) {
        $this->actingAs($owner)->post(route('comments.best', $pick))->assertRedirect();
    }

    // Only the current pick (A) is rewarded, exactly once.
    expect($post->fresh()->best_comment_id)->toBe($commentA->id)
        ->and($a->fresh()->reputation_points - $a0)->toBe(15)
        ->and($b->fresh()->reputation_points - $b0)->toBe(0)
        ->and($owner->fresh()->reputation_points - $o0)->toBe(2);

    $assertInvariant($owner, $a, $b);
});

it('leaves only the creation points after many vote flips and a final retraction', function () use ($assertInvariant) {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id, 'is_hidden' => false]); // +5 post_created
    $voter = User::factory()->create();

    foreach (range(1, 10) as $i) {
        Vote::castVote($voter, $post, $i % 2 === 1 ? 1 : -1);
    }
    Vote::where('user_id', $voter->id)->where('target_id', $post->id)->where('target_type', 'post')->first()->delete();

    expect($author->fresh()->reputation_points)->toBe(5);

    foreach (['post_upvoted', 'post_downvoted'] as $action) {
        expect(Reputation::where('user_id', $author->id)->where('action', "{$action}_reverted")->count())
            ->toBe(Reputation::where('user_id', $author->id)->where('action', $action)->count());
    }

    $assertInvariant($author);
});
