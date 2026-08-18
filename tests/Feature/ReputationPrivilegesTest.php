<?php

use App\Models\{User, Post};

/*
|--------------------------------------------------------------------------
| Reputation-gated participation privileges
|--------------------------------------------------------------------------
| Reputation unlocks PARTICIPATION capabilities over the user's own activity —
| never administrative authority over others. Thresholds (config/reputation.php):
| downvote 50, trusted 100, pin_post 250.
*/

// --- Downvoting (>= 50) -----------------------------------------------------

test('low-reputation scholars cannot downvote', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create(['reputation_points' => 0]);

    $this->actingAs($voter)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => -1])
        ->assertStatus(403);
});

test('low-reputation scholars may still upvote', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create(['reputation_points' => 0]);

    $this->actingAs($voter)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => 1])
        ->assertOk()
        ->assertJson(['success' => true]);
});

test('scholars with enough reputation may downvote', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $voter = User::factory()->create(['reputation_points' => 100]); // >= 50

    $this->actingAs($voter)
        ->postJson(route('vote'), ['type' => 'post', 'id' => $post->id, 'value' => -1])
        ->assertOk()
        ->assertJson(['success' => true]);
});

// --- Trusted friction bypass (>= 100) ---------------------------------------

test('non-trusted scholars are blocked by the external-link limit', function () {
    $user = User::factory()->create(['reputation_points' => 0]);

    $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'A curated set of external research resources',
        'body'  => 'Refer to https://a.com https://b.com https://c.com https://d.com https://e.com for the full dataset.',
    ])->assertSessionHasErrors('body');
});

test('trusted scholars bypass the external-link limit', function () {
    $user = User::factory()->create(['reputation_points' => 150]); // >= 100

    $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'A curated set of external research resources',
        'body'  => 'Refer to https://a.com https://b.com https://c.com https://d.com https://e.com for the full dataset.',
    ])->assertSessionHasNoErrors();

    expect(Post::where('title', 'A curated set of external research resources')->exists())->toBeTrue();
});

// --- Pinning own posts (>= 250) ---------------------------------------------

test('scholars with enough reputation can pin their own discussion', function () {
    $user = User::factory()->create(['reputation_points' => 300]); // >= 250
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(route('posts.pin', $post))->assertRedirect();

    expect($post->fresh()->pinned_at)->not->toBeNull();
});

test('low-reputation scholars cannot pin', function () {
    $user = User::factory()->create(['reputation_points' => 10]);
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(route('posts.pin', $post))->assertForbidden();

    expect($post->fresh()->pinned_at)->toBeNull();
});

test('a scholar cannot pin another scholars discussion', function () {
    $owner = User::factory()->create(['reputation_points' => 300]);
    $post = Post::factory()->create(['user_id' => $owner->id]);
    $other = User::factory()->create(['reputation_points' => 300]);

    $this->actingAs($other)->post(route('posts.pin', $post))->assertForbidden();
});

test('the feed shows pinned discussions first, ordered by most-recently-pinned', function () {
    $author = User::factory()->create();

    // Y is pinned earlier; X is pinned LATER (after Y); plus one unpinned post.
    Post::factory()->create([
        'user_id' => $author->id, 'status' => 'published', 'is_hidden' => false,
        'title' => 'Discussion Y pinned earlier',
        'pinned_at' => now()->subHour(),
    ]);
    Post::factory()->create([
        'user_id' => $author->id, 'status' => 'published', 'is_hidden' => false,
        'title' => 'Discussion X pinned later',
        'pinned_at' => now(),
    ]);
    Post::factory()->create([
        'user_id' => $author->id, 'status' => 'published', 'is_hidden' => false,
        'title' => 'An unpinned discussion',
        'created_at' => now(),
    ]);

    // X (pinned most recently) before Y (pinned earlier); both before the unpinned one.
    $this->get('/posts')
        ->assertOk()
        ->assertSeeInOrder([
            'Discussion X pinned later',
            'Discussion Y pinned earlier',
            'An unpinned discussion',
        ]);
});
