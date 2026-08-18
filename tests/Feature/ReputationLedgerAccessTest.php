<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Reputation ledger visibility
|--------------------------------------------------------------------------
| The owner may always view their own ledger. Moderators and admins may audit
| any scholar's ledger (oversight). Other users (and guests) are restricted.
*/

test('the owner can view their own reputation ledger', function () {
    $scholar = User::factory()->create();

    $this->actingAs($scholar)
        ->get(route('profile.reputation', $scholar->username))
        ->assertOk()
        ->assertDontSee('Restricted Ledger');
});

test('a moderator can audit another scholar\'s ledger', function () {
    $scholar = User::factory()->create();
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('profile.reputation', $scholar->username))
        ->assertOk()
        ->assertDontSee('Restricted Ledger');
});

test('an administrator can audit another scholar\'s ledger', function () {
    $scholar = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('profile.reputation', $scholar->username))
        ->assertOk()
        ->assertDontSee('Restricted Ledger');
});

test('a regular scholar cannot view another scholar\'s ledger', function () {
    $scholar = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($other)
        ->get(route('profile.reputation', $scholar->username))
        ->assertOk()
        ->assertSee('Restricted Ledger');
});

test('a guest cannot view a scholar\'s ledger', function () {
    $scholar = User::factory()->create();

    $this->get(route('profile.reputation', $scholar->username))
        ->assertOk()
        ->assertSee('Restricted Ledger');
});
