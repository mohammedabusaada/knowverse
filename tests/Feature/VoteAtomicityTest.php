<?php

use App\Events\RealTimeNotification;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;
use App\Services\ReputationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/*
|--------------------------------------------------------------------------
| Vote atomicity
|--------------------------------------------------------------------------
| A vote and its side effects (counters, reputation ledger, activity,
| notification) commit together or not at all. Genuine parallelism cannot
| be reproduced on SQLite; the knowverse:benchmark-concurrency experiment
| covers that on MariaDB. These tests pin the single-process behaviour.
*/

$failingLedger = fn (string $method) => new class($method) extends ReputationService
{
    public function __construct(private string $failOn) {}

    public function award(User $user, string $action, ?int $customDelta = null, ?\Illuminate\Database\Eloquent\Model $source = null, ?string $note = null): Reputation
    {
        if ($this->failOn === 'award') {
            throw new RuntimeException('ledger unavailable');
        }

        return parent::award($user, $action, $customDelta, $source, $note);
    }

    public function remove(User $user, string $action, ?\Illuminate\Database\Eloquent\Model $source = null): void
    {
        if ($this->failOn === 'remove') {
            throw new RuntimeException('ledger unavailable');
        }

        parent::remove($user, $action, $source);
    }
};

it('does not keep a vote whose reputation award failed', function () use ($failingLedger) {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();
    $pointsBefore = $author->fresh()->reputation_points;

    app()->instance(ReputationService::class, $failingLedger('award'));

    expect(fn () => Vote::castVote($voter, $post, 1))->toThrow(RuntimeException::class);

    expect(Vote::count())->toBe(0)
        ->and($post->fresh()->upvote_count)->toBe(0)
        ->and($author->fresh()->reputation_points)->toBe($pointsBefore);
});

it('keeps a vote whose retraction failed to reverse its reputation', function () use ($failingLedger) {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();
    Vote::castVote($voter, $post, 1);
    $pointsBefore = $author->fresh()->reputation_points;

    app()->instance(ReputationService::class, $failingLedger('remove'));

    expect(fn () => Vote::retract($voter, $post))->toThrow(RuntimeException::class);

    expect(Vote::count())->toBe(1)
        ->and($post->fresh()->upvote_count)->toBe(1)
        ->and($author->fresh()->reputation_points)->toBe($pointsBefore);
});

it('applies a repeated identical vote only once', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();

    Vote::castVote($voter, $post, 1);
    Vote::castVote($voter, $post, 1);

    expect(Reputation::where('user_id', $author->id)->where('action', 'post_upvoted')->count())->toBe(1)
        ->and($post->fresh()->upvote_count)->toBe(1);
});

it('treats retracting a vote that does not exist as a no-op', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();

    $this->actingAs($voter)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => 0])
        ->assertOk()
        ->assertJson(['upvotes' => 0, 'downvotes' => 0]);

    expect(Reputation::where('user_id', $author->id)->where('action', 'like', 'post_%voted%')->count())->toBe(0);
});

it('broadcasts the vote notification only after the vote commits', function () {
    Event::fake([RealTimeNotification::class]);
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create();

    try {
        DB::transaction(function () use ($voter, $post) {
            Vote::castVote($voter, $post, 1);
            throw new RuntimeException('rolled back by the caller');
        });
    } catch (RuntimeException) {
    }

    Event::assertNotDispatched(RealTimeNotification::class);
    expect(Vote::count())->toBe(0);

    Vote::castVote($voter, $post, 1);

    Event::assertDispatched(RealTimeNotification::class);
});
