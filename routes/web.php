<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;


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
// PUBLIC PROFILE ROUTE (e.g., /@mohammed)
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
    // Posts (full CRUD)
    // ----------------------
    Route::resource('posts', PostController::class);


    // ----------------------
    // Comments (store, update, delete)
    // ----------------------
    Route::resource('comments', CommentController::class)
        ->only(['store', 'update', 'destroy']);
});

// ----------------------
// Search
// ----------------------
Route::get('/search', [SearchController::class, 'index'])->name('search');

// ---------------------------------------------------------
// Auth routes (Breeze)
// ---------------------------------------------------------
require __DIR__ . '/auth.php';
