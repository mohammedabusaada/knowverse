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
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;

// ---------------------------------------------------------
// Home Page
// ---------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
});


// ---------------------------------------------------------
// Dashboard
// ---------------------------------------------------------
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ---------------------------------------------------------
// PUBLIC PROFILE ROUTES (e.g., /@mohammed)
// ---------------------------------------------------------
Route::get('/@{username}', [ProfileController::class, 'show'])
    ->name('profiles.show');


// ---------------------------------------------------------
// AUTHENTICATED ROUTES
// ---------------------------------------------------------
Route::middleware('auth')->group(function () {

    // ----------------------
    // Profile (edit own profile)
    // ----------------------
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // ----------------------
    // Reputation History
    // ----------------------
    Route::get('/@{user:username}/reputation', [ReputationController::class, 'index'])
    ->name('reputation.index');

    // ----------------------
    // Posts (full CRUD)
    // ----------------------
    Route::resource('posts', PostController::class);


    // ----------------------
    // Comments (store, update, delete)
    // ----------------------
    Route::resource('comments', CommentController::class)
        ->only(['store', 'update', 'destroy']);

    // Best Comment routes
    Route::post('/comments/{comment}/best', [CommentController::class, 'markAsBest'])
    ->name('comments.best');

    Route::post('/comments/{comment}/unbest', [CommentController::class, 'unmarkBest'])
    ->name('comments.unbest');


    // ----------------------
    // Voting (for posts/comments)
    // ----------------------
    Route::post('/vote', [VoteController::class, 'vote'])
        ->name('vote');


    // -----------------------------------------------------
    // TAGS (feature/tags-controller)
    // -----------------------------------------------------

    // CRUD (admin only - apply middleware/authorization in controller)
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

    // List all tags
    Route::get('/tags', [TagController::class, 'index']);

    // API: Search tags (for autocomplete)
    Route::get('/tags/search', [TagController::class, 'search']);

    // Attach tags to a post
    Route::post('/posts/{post}/tags', [TagController::class, 'attachTags']);

    // Follow a Tag
    Route::post('/tags/{tag}/follow', [TagController::class, 'follow']);
    Route::post('/tags/{tag}/unfollow', [TagController::class, 'unfollow']);

});

// ----------------------
// Search
// ----------------------
Route::get('/search', [SearchController::class, 'index'])->name('search');

// ---------------------------------------------------------
// Auth routes (Breeze)
// ---------------------------------------------------------
require __DIR__ . '/auth.php';
// Report submission
Route::middleware(['auth'])->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

// Admin panel for moderation
Route::middleware(['auth','can:manage-reports'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/reports', [ReportModerationController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportModerationController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/review', [ReportModerationController::class, 'review'])->name('reports.review');
    Route::patch('/reports/{report}/dismiss', [ReportModerationController::class, 'dismiss'])->name('reports.dismiss');
});
Route::get('/test-report', function () {
    return view('test-report');
});


Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
    });

