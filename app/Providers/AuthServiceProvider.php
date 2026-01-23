<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Post::class    => \App\Policies\PostPolicy::class,
        \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
        \App\Models\User::class    => \App\Policies\UserPolicy::class,
        \App\Models\UserActivity::class => \App\Policies\UserActivityPolicy::class,
        \App\Models\Notification::class => \App\Policies\NotificationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-reports', function (User $user) {
            return $user->role->name === 'admin';
        });
    }
}
