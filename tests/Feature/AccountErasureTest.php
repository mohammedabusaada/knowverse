<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;

/*
|--------------------------------------------------------------------------
| Account erasure - right to be forgotten
|--------------------------------------------------------------------------
| Permanent deletion removes the user's own account, votes and reputation
| ledger, keeps the content they authored without their identity, and
| retains the reputation other users earned from their votes - those ledger
| entries hold no personal data about the erased user. Vote counters on the
| content they voted on are corrected once their votes are gone.
*/

$assertInvariant = function (User ...$users) {
    foreach ($users as $user) {
        expect($user->fresh()->reputation_points)
            ->toBe((int) Reputation::where('user_id', $user->id)->sum('delta'));
    }
};

it('removes the erased user\'s account, votes and own reputation ledger', function () {
    $user = User::factory()->create(['reputation_points' => 0]);
    $other = User::factory()->create();
    $otherPost = Post::factory()->create(['user_id' => $other->id]);
    Post::factory()->create(['user_id' => $user->id]); // creates a ledger entry for $user
    Vote::castVote($user, $otherPost, 1);

    expect(Reputation::where('user_id', $user->id)->count())->toBeGreaterThan(0)
        ->and(Vote::where('user_id', $user->id)->count())->toBe(1);

    $this->actingAs($user)
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertRedirect('/');

    expect(User::withTrashed()->find($user->id))->toBeNull()
        ->and(Reputation::where('user_id', $user->id)->count())->toBe(0)
        ->and(Vote::where('user_id', $user->id)->count())->toBe(0);
});

it('keeps the discussions and comments the erased user authored, without their identity', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

    $user->forceDelete();

    $keptPost = Post::withoutGlobalScopes()->find($post->id);
    $keptComment = Comment::withoutGlobalScopes()->find($comment->id);

    expect($keptPost)->not->toBeNull()
        ->and($keptPost->user_id)->toBeNull()
        ->and($keptComment)->not->toBeNull()
        ->and($keptComment->user_id)->toBeNull();
});

it('retains the reputation other users earned from the erased user\'s votes', function () use ($assertInvariant) {
    $author = User::factory()->create(['reputation_points' => 0]);
    $post = Post::factory()->create(['user_id' => $author->id]); // +5 post_created
    $voter = User::factory()->create();
    Vote::castVote($voter, $post, 1); // +5 post_upvoted

    $voter->forceDelete();

    expect($author->fresh()->reputation_points)->toBe(10)
        ->and(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted')->count())->toBe(1)
        ->and(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted_reverted')->count())->toBe(0);

    $assertInvariant($author);
});

it('recalculates vote counters on content the erased user had voted on', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $author->id]);
    $voter = User::factory()->create();
    $remaining = User::factory()->create();

    Vote::castVote($voter, $post, 1);
    Vote::castVote($remaining, $post, 1);
    Vote::castVote($voter, $comment, -1);

    expect($post->fresh()->upvote_count)->toBe(2)
        ->and($comment->fresh()->downvote_count)->toBe(1);

    $voter->forceDelete();

    expect($post->fresh()->upvote_count)->toBe(1)
        ->and($comment->fresh()->downvote_count)->toBe(0);
});
