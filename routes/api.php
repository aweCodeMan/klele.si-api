<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('verify', [UserController::class, 'verify'])->name('verification.verify');

Route::post('forgot-password', [UserController::class, 'forgotPassword'])->name('forgot-password')->middleware('guest');
Route::post('password-reset', [UserController::class, 'resetPassword'])->name('password.reset')->middleware('guest');

Route::get('posts/{slug}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
Route::get('feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed');

Route::name('groups.')->prefix('groups')->group(function() {
    Route::get('/', [\App\Http\Controllers\GroupController::class, 'index'])->name('index');
});

Route::post('markdown', [\App\Http\Controllers\MarkdownController::class, 'transform'])->name('markdown')->middleware('auth');

Route::name('comments.')->prefix('comments')->group(function (){
   Route::middleware(['auth', 'verified'])->group(function (){
       Route::put('{uuid}', [\App\Http\Controllers\CommentController::class, 'update'])->name('update');
   });
});

Route::name('posts.')->prefix('posts')->group(function() {
    Route::middleware(['auth', 'verified'])->group(function (){
        Route::post('/', [\App\Http\Controllers\PostController::class, 'store'])->name('store');
        Route::put('{uuid}', [\App\Http\Controllers\PostController::class, 'update'])->name('update');
        Route::get('{uuid}/form', [\App\Http\Controllers\PostController::class, 'form'])->name('form');
        Route::delete('{uuid}', [\App\Http\Controllers\PostController::class, 'delete'])->name('delete');

        Route::post('{postUuid}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    });
});

Route::name('votes.')->prefix('votes')->group(function() {
    Route::middleware(['auth', 'verified'])->group(function (){
        Route::post('/', [\App\Http\Controllers\VoteController::class, 'store'])->name('store');
    });
});

Route::name('users.')->prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');

    Route::middleware(['auth'])->group(function () {
        Route::post('reverify', [UserController::class, 'reverify'])->name('reverify');

        Route::put('/', [UserController::class, 'update'])->name('update');
        Route::get('show', [UserController::class, 'show'])->name('show');
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
    });
});



