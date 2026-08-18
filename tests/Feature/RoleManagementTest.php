<?php

use App\Models\User;
use App\Services\ReputationService;

/*
|--------------------------------------------------------------------------
| RBAC role management — manual, admin-only, and decoupled from reputation
|--------------------------------------------------------------------------
| Administrative authority is granted ONLY by an administrator, never earned
| through reputation. This is the security separation the design relies on.
*/

test('an administrator can change a user role', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(); // role_id 1

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $user), ['role_id' => 3])
        ->assertRedirect();

    expect($user->fresh()->role_id)->toBe(3);
});

test('a moderator cannot change roles (admin-only)', function () {
    $moderator = User::factory()->moderator()->create();
    $user = User::factory()->create();

    $this->actingAs($moderator)
        ->patch(route('admin.users.update-role', $user), ['role_id' => 2])
        ->assertForbidden();

    expect($user->fresh()->role_id)->toBe(1);
});

test('a standard user cannot reach the role-management route', function () {
    $user = User::factory()->create();
    $victim = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.users.update-role', $victim), ['role_id' => 2])
        ->assertForbidden();

    expect($victim->fresh()->role_id)->toBe(1);
});

test('an admin cannot change their own role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $admin), ['role_id' => 1]);

    expect($admin->fresh()->role_id)->toBe(2); // unchanged
});

test('reputation never changes a user role automatically (decoupled by design)', function () {
    $user = User::factory()->create(['reputation_points' => 0, 'role_id' => 1]);

    // Earn far beyond every title threshold through the normal ledger path.
    app(ReputationService::class)->award($user, 'manual_test_grant', 5000);

    expect($user->fresh()->reputation_points)->toBe(5000);
    expect($user->fresh()->role_id)->toBe(1); // still a standard user — RBAC is decoupled
});
