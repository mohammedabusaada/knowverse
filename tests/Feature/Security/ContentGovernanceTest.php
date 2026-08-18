<?php

use App\Models\{User, Post};

/*
|--------------------------------------------------------------------------
| Content governance & authentication hardening
|--------------------------------------------------------------------------
| Lexical filtering, link-spam constraints, reserved-username protection,
| password strength, and brute-force rate limiting.
*/

test('discussions containing blocked words are rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'An idiot guide to academic writing', // contains a blocked term
            'body'  => 'This body is long enough to satisfy the minimum content length requirement.',
        ])
        ->assertSessionHasErrors('title');

    expect(Post::where('title', 'like', '%idiot%')->exists())->toBeFalse();
});

test('discussions with too many external links are rejected as spam', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'A collection of excellent external resources',
            'body'  => 'See https://a.com https://b.com https://c.com https://d.com https://e.com for details.',
        ])
        ->assertSessionHasErrors('body');

    expect(Post::where('title', 'A collection of excellent external resources')->exists())->toBeFalse();
});

test('reserved system usernames cannot be registered', function () {
    $this->post('/register', [
        'username'              => 'admin',
        'full_name'             => 'Imposter Admin',
        'email'                 => 'imposter@example.com',
        'password'              => 'Password123',
        'password_confirmation' => 'Password123',
    ])->assertSessionHasErrors('username');

    expect(User::where('username', 'admin')->exists())->toBeFalse();
});

test('weak passwords are rejected at registration', function () {
    $this->post('/register', [
        'username'              => 'newscholar',
        'full_name'             => 'New Scholar',
        'email'                 => 'new@example.com',
        'password'              => 'weak',
        'password_confirmation' => 'weak',
    ])->assertSessionHasErrors('password');
});

test('login is rate limited after repeated failures (brute-force defense)', function () {
    $user = User::factory()->create();

    // Five failed attempts exhaust the limiter.
    foreach (range(1, 5) as $i) {
        $this->post('/login', ['login' => $user->email, 'password' => 'wrong-password']);
    }

    // The sixth attempt is blocked even with the CORRECT password — proving the
    // throttle, not merely a credential mismatch.
    $this->post('/login', ['login' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});
