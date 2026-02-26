<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\UserController;

// ==================== AUTH ====================
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/verify_token',    [AuthController::class, 'verifyToken']);
Route::post('/logout',          [AuthController::class, 'logout']);
Route::post('/profile/update',  [AuthController::class, 'updateProfile']);
Route::post('/profile/avatar',  [AuthController::class, 'uploadAvatar']);
Route::post('/change-password', [ProfileController::class, 'changePassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// ==================== ADMIN AUTH ====================
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// ==================== CATEGORIES ====================
Route::get('/categories', [CategoryController::class, 'index']);

// ==================== BOOKS (public) ====================
Route::get('/books',        [BookController::class, 'index']);
Route::get('/books/{slug}', [BookController::class, 'show']);

// ==================== CHAPTERS (butuh login) ====================
Route::get('/books/{slug}/chapters/{chapter_number}', [ChapterController::class, 'show']);

// ==================== REVIEWS ====================
Route::get('/books/{bookId}/reviews',  [ReviewController::class, 'index']);
Route::post('/books/{bookId}/reviews', [ReviewController::class, 'store']);
Route::delete('/reviews/{id}',         [ReviewController::class, 'destroy']);

// ==================== COMMENTS ====================
Route::get('/books/{bookId}/comments',  [CommentController::class, 'index']);
Route::post('/books/{bookId}/comments', [CommentController::class, 'store']);
Route::delete('/comments/{id}',         [CommentController::class, 'destroy']);

// ==================== BOOKMARKS ====================
Route::get('/bookmarks',             [BookmarkController::class, 'index']);
Route::post('/bookmarks',            [BookmarkController::class, 'store']);
Route::delete('/bookmarks/{bookId}', [BookmarkController::class, 'destroy']);

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->group(function () {
    // Statistik dashboard
    Route::get('/statistics', [StatisticController::class, 'index']);

    // Kelola buku
    Route::get('/books',          [BookController::class, 'adminIndex']);
    Route::post('/books',         [BookController::class, 'store']);
    Route::get('/books/{id}',     [BookController::class, 'adminShow']);
    Route::post('/books/{id}',    [BookController::class, 'update']);  // POST karena pakai FormData
    Route::delete('/books/{id}',  [BookController::class, 'destroy']);

    // Kelola chapter
    Route::post('/books/{bookId}/chapters', [ChapterController::class, 'store']);
    Route::put('/chapters/{id}',            [ChapterController::class, 'update']);
    Route::delete('/chapters/{id}',         [ChapterController::class, 'destroy']);

    // Kelola kategori
    Route::post('/categories',        [CategoryController::class, 'store']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Data anggota
    Route::get('/users', [UserController::class, 'index']);
});