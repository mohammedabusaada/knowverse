<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ReputationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\ReportModerationController;
use App\Http\Controllers\HomeController;

// ---------------------------------------------------------
// 1. Static & Fixed Routes (Highest Priority)
// ---------------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ---------------------------------------------------------
// 2. Authentication System (Breeze/Jetstream)
// ---------------------------------------------------------
// We require this BEFORE the profile wildcard so that /login and /register 
// are caught by the auth controller, not the profile controller.
require __DIR__ . '/auth.php';

// ---------------------------------------------------------
// 3. Admin Routes
// ---------------------------------------------------------
Route::middleware(['auth', 'can:manage-reports'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/reports', [ReportModerationController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [ReportModerationController::class, 'show'])->name('reports.show');
        Route::patch('/reports/{report}/review', [ReportModerationController::class, 'review'])->name('reports.review');
        Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])->name('reports.dismiss');
    });

// ---------------------------------------------------------
// 4. Authenticated User Routes
// ---------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Profile Settings (Edit own)
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Posts & Comments
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);

    // Best Comment logic
    Route::post('/comments/{comment}/best', [CommentController::class, 'markAsBest'])->name('comments.best');
    Route::post('/comments/{comment}/unbest', [CommentController::class, 'unmarkBest'])->name('comments.unbest');

    // Interactions
    Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // Tags Management
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/search', [TagController::class, 'search']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
    Route::post('/posts/{post}/tags', [TagController::class, 'attachTags']);
    Route::post('/tags/{tag}/follow', [TagController::class, 'follow']);
    Route::post('/tags/{tag}/unfollow', [TagController::class, 'unfollow']);
    
    // Reputation History
    Route::get('/{user:username}/reputation', [ReputationController::class, 'index'])->name('reputation.index');
});

// ---------------------------------------------------------
// 5. Public Profile Routes (Wildcards MUST be Last)
// ---------------------------------------------------------
// We use {user:username} to tell Laravel to find the user by their username column.
// By placing this at the bottom, Laravel only checks this if no other route above matches.
Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profiles.show');

// ---------------------------------------------------------
// 6. Debug / Development
// ---------------------------------------------------------
Route::get('/test-report', function () {
    return view('test-report');
});