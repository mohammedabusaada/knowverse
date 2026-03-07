<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Map Domain Entities to their respective Role-Based Access Control (RBAC) Policies.
     */
    protected $policies = [
        \App\Models\Post::class         => \App\Policies\PostPolicy::class,
        \App\Models\Comment::class      => \App\Policies\CommentPolicy::class,
        \App\Models\User::class         => \App\Policies\UserPolicy::class,
        \App\Models\UserActivity::class => \App\Policies\UserActivityPolicy::class,
        \App\Models\Notification::class => \App\Policies\NotificationPolicy::class,
        \App\Models\Report::class       => \App\Policies\ReportPolicy::class,
    ];

    /**
     * Register authorization services and global security gates.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Global Authorization Interceptor (Gateway)
         * -------------------------------
         * This logic runs before any other authorization checks. 
         * If a user is flagged as banned, all 'write' permissions are globally revoked.
         */
        Gate::before(function (User $user, string $ability) {
            if ($user->isBanned) { // Using the updated dynamic accessor
                return false; // Denial of service for all authorized actions
            }
        });

        /**
         * Moderator Access Definition
         * ---------------------------
         * Grants administrative oversight capabilities for managing reports.
         */
        Gate::define('manage-reports', function (User $user) {
            return $user->canModerate();
        });
    }
}