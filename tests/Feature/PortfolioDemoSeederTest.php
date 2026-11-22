<?php

use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use Database\Seeders\PortfolioDemoSeeder;

it('builds repeatable portfolio fixtures with ledger-backed balances and real reversals', function () {
    $this->seed(PortfolioDemoSeeder::class);
    $scholars = User::where('username', 'like', 'demo_%')->get();
    expect($scholars)->toHaveCount(6);
    expect(Post::count())->toBe(10);
    foreach ($scholars as $scholar) {
        expect($scholar->reputation_points)->toBe((int) $scholar->reputations()->sum('delta'));
    }
    $reversal = Reputation::where('action', 'post_upvoted_reverted')->sole();
    $original = Reputation::findOrFail($reversal->reverses_id);
    expect($reversal->delta)->toBe(-$original->delta);
    $sara = $scholars->firstWhere('username', 'demo_sara');
    $this->actingAs($sara)->get('/demo_sara/reputation')
        ->assertOk()
        ->assertSee('Discussion Upvote Retracted')
        ->assertSee('View source')
        ->assertSee('Designing a reputation ledger that can explain every point');
    $counts = [Post::count(), Reputation::count(), User::count()];
    $this->seed(PortfolioDemoSeeder::class);
    expect([Post::count(), Reputation::count(), User::count()])->toBe($counts);
});

it('refuses portfolio fixtures outside local and testing environments', function () {
    $this->app->instance('env', 'production');
    expect(fn () => app(PortfolioDemoSeeder::class)->run())
        ->toThrow(RuntimeException::class, 'restricted');
    expect(User::count())->toBe(0);
});
