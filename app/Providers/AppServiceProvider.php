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
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Vote::observe(VoteObserver::class);
        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);

        Blade::directive('highlight', function ($expression) {
        return "<?php 
            echo preg_replace(
                '/(' . preg_quote(request('q'), '/') . ')/i', 
                '<mark class=\"bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded px-0.5\">$1</mark>', 
                $expression
            ); 
        ?>";
    });
    }
}
