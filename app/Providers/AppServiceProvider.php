<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use App\Models\Vote;
use App\Models\Post;
use App\Models\Comment;
use App\Observers\VoteObserver;
use App\Observers\PostObserver;
use App\Observers\CommentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Vote::observe(VoteObserver::class);
        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);

    }
}
