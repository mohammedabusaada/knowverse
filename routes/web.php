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
use App\Http\Controllers\ImageUploadController;

/*
|--------------------------------------------------------------------------
| 1. Static & Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Publicly readable discussions
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->whereNumber('post');

// Topic & Tag Exploration
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/search', [TagController::class, 'search'])->name('tags.search');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/tags/{tag}/followers', [TagController::class, 'followers'])->name('tags.followers');

/*
|--------------------------------------------------------------------------
| 2. Authentication
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 3. Admin & Moderation Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);
        
        Route::patch('/users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');

        // Tags Management
        Route::get('/tags', [\App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [\App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [\App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [\App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.destroy');

        // Reports
        Route::middleware('can:manage-reports')->group(function () {
            Route::get('/reports', [ReportModerationController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}', [ReportModerationController::class, 'show'])->name('reports.show');
            Route::patch('/reports/{report}/resolve', [ReportModerationController::class, 'resolve'])->name('reports.resolve');
            Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])->name('reports.dismiss');
        });
    });

/*
|--------------------------------------------------------------------------
| 4. Authenticated Scholar Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\CheckBanned::class])->group(function () {

    Route::get('/saved-posts', [SavedPostController::class, 'index'])->name('posts.saved');

    // Notifications Engine
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
        Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
        Route::get('/{notification}/visit', [NotificationController::class, 'visit'])->name('visit');
        Route::delete('/', [NotificationController::class, 'clear'])->name('clear');
    });

    // Scholar Settings & Profile Management
    Route::prefix('settings')->group(function () {
        // Identity Management
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Notification Preferences - FIXED: Changed to PUT to match the form @method('PUT')
        Route::get('/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications');
        Route::put('/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');
        
        // Security & Standing
        Route::get('/security', function () { return view('settings.security'); })->name('settings.security');
        Route::patch('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    });

    /*
    |--------------------------------------------------------------------------
    | 5. Verified Access (Scholarly Contributions)
    |--------------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {

        // Discussion & Response Management
        Route::resource('posts', PostController::class)->except(['show', 'index']);
        Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
        
        // Content Curation: Author's Pick (Consensus)
        Route::post('/comments/{comment}/highlight', [CommentController::class, 'markAsBest'])->name('comments.best');
        Route::post('/comments/{comment}/unhighlight', [CommentController::class, 'unmarkBest'])->name('comments.unbest');

        // Social & Peer Review
        Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::post('/posts/{post}/save', [SavedPostController::class, 'toggle'])->name('posts.save.toggle');

        // Network Expansion
        Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
        
        // Topic Subscription - FIXED: unified route naming
        Route::post('/tags/{tag}/follow', [TagFollowController::class, 'follow'])->name('tags.follow');
        Route::delete('/tags/{tag}/follow', [TagFollowController::class, 'unfollow'])->name('tags.unfollow');

        // Content Media
        Route::post('/upload-image', [ImageUploadController::class, 'store'])->name('images.upload');
    });
});

/*
|--------------------------------------------------------------------------
| 6. Public Profile Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/{user}')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/activity', [UserActivityController::class, 'index'])->name('profile.activity');
    Route::get('/reputation', [ReputationController::class, 'index'])->name('profile.reputation');
    Route::get('/followers', [ProfileController::class, 'followers'])->name('profile.followers');
    Route::get('/following', [ProfileController::class, 'following'])->name('profile.following');
})->where('user', '[a-zA-Z0-9._-]+');