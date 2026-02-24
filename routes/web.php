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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportModerationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\TagFollowController;
use App\Http\Controllers\SavedPostController;

/*
|--------------------------------------------------------------------------
| 1. Static & Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Publicly readable content
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::get('/posts/{post}', [PostController::class, 'show'])
    ->name('posts.show')
    ->whereNumber('post');

Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

/*
|--------------------------------------------------------------------------
| 2. Authentication
|--------------------------------------------------------------------------
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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('can:manage-reports')->group(function () {
            Route::get('/reports', [ReportModerationController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}', [ReportModerationController::class, 'show'])->name('reports.show');
            Route::patch('/reports/{report}/resolve', [ReportModerationController::class, 'resolve'])->name('reports.resolve');
            Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])->name('reports.dismiss');
        });

        Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| 4. Authenticated Routes (Unverified Users OK)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/saved-posts', [SavedPostController::class, 'index'])
        ->name('saved-posts.index');


    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
        Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
        Route::get('/{notification}/visit', [NotificationController::class, 'visit'])->name('visit');
        Route::delete('/', [NotificationController::class, 'clear'])->name('clear');
    });

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications');
    Route::post('/settings/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');

    Route::get('/settings/security', function () { return view('settings.security'); })->name('settings.security');

    /*
    |--------------------------------------------------------------------------
    | 5. VERIFIED Routes (Strict "Write" Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {

        Route::resource('posts', PostController::class)->except(['show', 'index']);
        Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
        Route::post('/posts/{post}/tags', [TagController::class, 'attachTags'])->name('posts.tags.attach');

        Route::post('/comments/{comment}/best', [CommentController::class, 'markAsBest'])->name('comments.best');
        Route::post('/comments/{comment}/unbest', [CommentController::class, 'unmarkBest'])->name('comments.unbest');

        Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::post('/posts/{post}/save', [SavedPostController::class, 'toggle'])->name('posts.save.toggle');

        Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
        Route::post('/tags/{tag}/follow', [TagFollowController::class, 'follow'])->name('tags.follow');
        Route::delete('/tags/{tag}/follow', [TagFollowController::class, 'unfollow'])->name('tags.unfollow');

        Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 6. Public Profile & Activity Routes
|--------------------------------------------------------------------------
*/
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/search', [TagController::class, 'search'])->name('tags.search');
Route::get('/tags/{tag}/followers', [TagController::class, 'followers'])->name('tags.followers');

Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/{user:username}/activity', [UserActivityController::class, 'index'])->name('profile.activity');
Route::get('/{user:username}/reputation', [ReputationController::class, 'index'])->name('profile.reputation');
Route::get('/{user:username}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/{user:username}/following', [ProfileController::class, 'following'])->name('profile.following');
