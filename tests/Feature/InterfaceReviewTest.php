<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

it('preserves topic filters across discussion pages and marks the current page', function () {
    $tag = Tag::factory()->create(['name' => "Researcher's methods"]);
    $author = User::factory()->create();
    Post::factory()->count(10)->for($author)->create()->each(fn ($post) => $post->tags()->attach($tag));

    $this->get(route('posts.index', ['tags' => [$tag->name]]))
        ->assertOk()
        ->assertSee('aria-current="page"', false)
        ->assertSee('rel="next"', false)
        ->assertSee('page=2', false)
        ->assertSee('tags%5B0%5D=Researcher', false);

    $this->get(route('posts.index', ['tags' => [$tag->name], 'page' => 2]))
        ->assertOk()
        ->assertSee('Showing 10–10 of 10')
        ->assertSee('rel="prev"', false)
        ->assertDontSee('rel="next"', false);
});

it('offers recovery when a discussion page is outside the available results', function () {
    Post::factory()->create();
    $this->get('/posts?page=999')->assertOk()->assertSee('Back to first page');
    $this->get('/posts?tags[]=missing-topic')->assertOk()->assertSee('View all discussions');
});

it('renders account and administration review pages for their authorized roles', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    foreach (['/saved-posts', '/notifications', '/settings/profile', '/settings/notifications', '/settings/security', '/posts/create', '/'.$user->username, '/'.$user->username.'/activity', '/'.$user->username.'/reputation', '/'.$user->username.'/followers', '/'.$user->username.'/following'] as $path) {
        $this->get($path)->assertOk();
    }
    $this->get('/admin/dashboard')->assertForbidden();
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);
    foreach (['/admin/dashboard', '/admin/users', '/admin/tags', '/admin/reports'] as $path) {
        $this->get($path)->assertOk();
    }
});
