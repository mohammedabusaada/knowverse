<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Profile & identity — KnowVerse customised behaviour
|--------------------------------------------------------------------------
| These replace the Breeze boilerplate to match the actual implementation:
| settings live under /settings/profile, the update route is PUT, the field
| is full_name (not name), email is IMMUTABLE, and account deletion is a GDPR
| hard delete guarded by the current password.
*/

test('profile settings page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'full_name' => 'Updated Scholar',
        'username'  => 'updated_scholar',
        'email'     => $user->email, // required by the validator; immutable in the controller
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.show', 'updated_scholar'));

    $user->refresh();
    expect($user->full_name)->toBe('Updated Scholar');
    expect($user->username)->toBe('updated_scholar');
});

test('email address is immutable and cannot be changed via a profile update', function () {
    $user = User::factory()->create();
    $original = $user->email;

    $this->actingAs($user)->put(route('profile.update'), [
        'full_name' => $user->full_name,
        'username'  => $user->username,
        'email'     => 'attacker-changed@example.com',
    ])->assertSessionHasNoErrors();

    $user->refresh();
    expect($user->email)->toBe($original);
    expect($user->email_verified_at)->not->toBeNull();
});

test('a user can permanently delete their account (GDPR right to be forgotten)', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertGuest();

    // forceDelete() — the record is gone, including from soft-deleted scope.
    expect(User::withTrashed()->find($user->id))->toBeNull();
});

test('the correct password is required to delete an account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), ['password' => 'wrong-password'])
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect(User::find($user->id))->not->toBeNull();
});
