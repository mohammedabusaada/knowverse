<?php

use App\Models\Post;
use App\Models\User;

/*
 * Pure unit coverage for the reputation-gated privilege boundaries. These assert
 * the configured thresholds directly, with no database involvement, so a change to
 * config/reputation.php that silently widens access fails here first.
 */

it('withholds every privilege from a scholar with no reputation', function () {
    $user = new User(['reputation_points' => 0]);

    expect($user->hasPrivilege('downvote'))->toBeFalse()
        ->and($user->hasPrivilege('trusted'))->toBeFalse()
        ->and($user->hasPrivilege('pin_post'))->toBeFalse();
});

it('unlocks each privilege exactly at its configured threshold', function (string $privilege) {
    $threshold = config("reputation.privileges.$privilege");

    expect((new User(['reputation_points' => $threshold - 1]))->hasPrivilege($privilege))->toBeFalse()
        ->and((new User(['reputation_points' => $threshold]))->hasPrivilege($privilege))->toBeTrue();
})->with(['downvote', 'trusted', 'pin_post']);

it('grants the higher privileges to a scholar above every threshold', function () {
    $user = new User(['reputation_points' => 10_000]);

    expect($user->hasPrivilege('downvote'))->toBeTrue()
        ->and($user->hasPrivilege('trusted'))->toBeTrue()
        ->and($user->hasPrivilege('pin_post'))->toBeTrue();
});

it('denies an unknown privilege key rather than defaulting to allow', function () {
    expect((new User(['reputation_points' => 10_000]))->hasPrivilege('delete_platform'))->toBeFalse();
});

it('reports a discussion as pinned only while pinned_at is set', function () {
    expect((new Post(['pinned_at' => null]))->isPinned())->toBeFalse()
        ->and((new Post(['pinned_at' => now()]))->isPinned())->toBeTrue();
});
