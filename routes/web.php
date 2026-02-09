<?php

use Illuminate\Support\Facades\Route;

/**
 * Controllers
 */
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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportModerationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\TagFollowController;

/*
|--------------------------------------------------------------------------
| 1. Static & Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

/*
|--------------------------------------------------------------------------
| 2. Authentication (Breeze / Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 3. Admin Routes
|--------------------------------------------------------------------------
| Protected by auth and custom is_admin middleware.
*/

Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Moderation Management
        Route::middleware('can:manage-reports')->group(function () {
            Route::get('/reports', [ReportModerationController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}', [ReportModerationController::class, 'show'])->name('reports.show');
            Route::patch('/reports/{report}/resolve', [ReportModerationController::class, 'resolve'])->name('reports.resolve');
            Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])->name('reports.dismiss');
        });

        // Users management
        Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);
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

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
        Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
        Route::get('/{notification}/visit', [NotificationController::class, 'visit'])->name('visit');
        Route::delete('/', [NotificationController::class, 'clear'])->name('clear');
    });

    // Settings & Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications');
    Route::post('/settings/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');

    // Content Resources
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::post('/posts/{post}/tags', [TagController::class, 'attachTags'])->name('posts.tags.attach');

    // Best Answer Logic
    Route::post('/comments/{comment}/best', [CommentController::class, 'markAsBest'])->name('comments.best');
    Route::post('/comments/{comment}/unbest', [CommentController::class, 'unmarkBest'])->name('comments.unbest');

    // Interactions (Votes & Reports)
    Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // Tags Management
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::get('/search', [TagController::class, 'search'])->name('search');
        Route::post('/', [TagController::class, 'store'])->name('store');
        Route::put('/{tag}', [TagController::class, 'update'])->name('update');
        Route::delete('/{tag}', [TagController::class, 'destroy'])->name('destroy');
        Route::post('/{tag}/follow', [TagFollowController::class, 'follow'])->name('follow');
        Route::delete('/{tag}/follow', [TagFollowController::class, 'unfollow'])->name('unfollow');
        Route::get('/{tag}/followers', [TagController::class, 'followers'])->name('followers');
    Route::get('/{tag:slug}', [TagController::class, 'show'])->name('show');
        });

    // Social (Followers)
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');

    Route::get('/settings/security', function () {
        return view('settings.security');
    })->name('settings.security');
});

/*
|--------------------------------------------------------------------------
| 5. Public Profile & Activity Routes (Wildcards MUST stay at the bottom)
|--------------------------------------------------------------------------
*/

Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/{user:username}/activity', [UserActivityController::class, 'index'])->name('profile.activity');
Route::get('/{user:username}/reputation', [ReputationController::class, 'index'])->name('profile.reputation');
Route::get('/{user:username}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/{user:username}/following', [ProfileController::class, 'following'])->name('profile.following');