<?php

use App\Models\{User, Post, Comment, Notification};
use App\Enums\NotificationType;

/*
|--------------------------------------------------------------------------
| OWASP A01 — Broken Access Control
|--------------------------------------------------------------------------
| Validates the multi-tier RBAC model (Guest / Unverified / Verified /
| Moderator / Admin), ownership enforcement, IDOR resistance, and the
| banned-account interceptor.
*/

test('guests are redirected to login from the admin area', function () {
    $this->get('/admin/dashboard')->assertRedirect(route('login'));
});

test('verified users without privileges are forbidden from the admin dashboard', function () {
    $user = User::factory()->create(); // standard scholar (role 1)

    $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
});

test('moderators may reach the dashboard but NOT user management', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)->get('/admin/dashboard')->assertOk();
    $this->actingAs($moderator)->get('/admin/users')->assertForbidden();
});

test('administrators may manage users', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

test('a scholar cannot edit another scholar\'s discussion', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $attacker = User::factory()->create();

    $this->actingAs($attacker)
        ->put(route('posts.update', $post), [
            'title' => 'A hijacked title that is long enough',
            'body'  => 'This body is sufficiently long to satisfy the minimum length validation rule.',
        ])
        ->assertForbidden();
});

test('a moderator may edit any discussion (delegated moderation authority)', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->put(route('posts.update', $post), [
            'title' => 'A moderator-corrected scholarly title',
            'body'  => 'A sufficiently long and clean discussion body edited by a moderator for compliance.',
        ])
        ->assertRedirect(route('posts.show', $post));
});

test('a scholar cannot delete another scholar\'s comment', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id, 'is_hidden' => false]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $owner->id, 'is_hidden' => false]);
    $attacker = User::factory()->create();

    $this->actingAs($attacker)->delete(route('comments.destroy', $comment))->assertForbidden();
});

test('a user cannot mark another user\'s notification as read (IDOR)', function () {
    $victim = User::factory()->create();
    $notification = Notification::create([
        'user_id' => $victim->id,
        'type'    => NotificationType::SYSTEM,
        'is_read' => false,
    ]);
    $attacker = User::factory()->create();

    $this->actingAs($attacker)
        ->post(route('notifications.read', $notification))
        ->assertForbidden();
});

test('unverified users cannot create discussions', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertRedirect(route('verification.notice'));
});

test('banned users are logged out and denied access on every request', function () {
    $banned = User::factory()->create(['banned_at' => now()]);

    $this->actingAs($banned)->get(route('posts.create'))->assertRedirect(route('login'));
    $this->assertGuest();
});
