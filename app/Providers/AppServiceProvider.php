<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }

    protected $policies = [
    \App\Models\Post::class => \App\Policies\PostPolicy::class,
    \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
];
}
