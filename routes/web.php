<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReputationController;
use App\Http\Controllers\UserActivityController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportModerationController;
use App\Http\Controllers\NotificationPreferenceController;

/*
|--------------------------------------------------------------------------
| 1. Static & Fixed Routes (Highest Priority)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])
    ->name('search.suggestions');

/*
|--------------------------------------------------------------------------
| 2. Authentication (Breeze / Jetstream)
|--------------------------------------------------------------------------
| MUST come before username wildcard routes
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 3. Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::middleware('can:manage-reports')->group(function () {
            Route::get('/reports', [ReportModerationController::class, 'index'])
                ->name('reports.index');

            Route::get('/reports/{report}', [ReportModerationController::class, 'show'])
                ->name('reports.show');

            Route::patch('/reports/{report}/review', [ReportModerationController::class, 'review'])
                ->name('reports.review');

            Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])
                ->name('reports.dismiss');
        });
    });

/*
|--------------------------------------------------------------------------
| 4. Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    | Profile settings (own account only)
    */
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    /*
    | Content
    */
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class)
        ->only(['store', 'update', 'destroy']);

    /*
    | Best Answer
    */
    Route::post('/comments/{comment}/best', [CommentController::class, 'markAsBest'])
        ->name('comments.best');

    Route::post('/comments/{comment}/unbest', [CommentController::class, 'unmarkBest'])
        ->name('comments.unbest');

    /*
    | Interactions
    */
    Route::post('/vote', [VoteController::class, 'vote'])
        ->name('vote');

    Route::post('/reports', [ReportController::class, 'store'])
        ->name('reports.store');

    /*
    | Tags
    */
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/search', [TagController::class, 'search']);

    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

    Route::post('/posts/{post}/tags', [TagController::class, 'attachTags']);

    Route::post('/tags/{tag}/follow', [TagController::class, 'follow']);
    Route::post('/tags/{tag}/unfollow', [TagController::class, 'unfollow']);
/*
| Notification Preferences
*/
Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])
    ->name('settings.notifications');

Route::post('/settings/notifications', [NotificationPreferenceController::class, 'update'])
    ->name('settings.notifications.update');

    /*
    | Reputation
    */
    Route::get('/{user:username}/reputation', [ReputationController::class, 'index'])
        ->name('reputation.index');
});

/*
|--------------------------------------------------------------------------
| 5. Public Profile & Activity Routes (MUST be last)
|--------------------------------------------------------------------------
| GitHub-style usernames:
|   /username
|   /username/activity
*/


Route::get('/{user:username}/activity', [UserActivityController::class, 'index'])
    ->name('activity.index');

Route::get('/{user:username}', [ProfileController::class, 'show'])
    ->name('profiles.show');

/*
|--------------------------------------------------------------------------
| 6. Debug / Development
|--------------------------------------------------------------------------
*/

Route::get('/test-report', function () {
    return view('test-report');
});

Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
    });

