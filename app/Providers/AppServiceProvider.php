<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

// Authentication Notifications
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Vote;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Observers\VoteObserver;
use App\Observers\PostObserver;
use App\Observers\UserObserver;
use App\Observers\CommentObserver;

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

        // Define global password security defaults
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers()
                ->mixedCase();
        });

        // Map polymorphic relations
        Relation::morphMap([
            'post' => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
            'user' => \App\Models\User::class,
        ]);

        // Register model observers
        User::observe(UserObserver::class);
        Vote::observe(VoteObserver::class);
        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);

        // Customize the Verification Email Notification
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address - ' . config('app.name'))
                ->greeting('Hello ' . $notifiable->username . '!')
                ->line('Please click the button below to verify your email address.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create an account, no further action is required.')
                ->salutation("Regards,\n" . config('app.name') . ' Academic Community');
        });

        // Register custom Blade directives (Monochrome Update)
        Blade::directive('highlight', function ($expression) {
            return "<?php
                echo preg_replace(
                    '/(' . preg_quote(request('q'), '/') . ')/i',
                    '<mark class=\"bg-black text-white dark:bg-white dark:text-black px-1 rounded-sm font-bold\">$1</mark>',
                    $expression
                );
            ?>";
        });
    }
}