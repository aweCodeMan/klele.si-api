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


Route::name('users.')->prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');

    Route::middleware(['auth'])->group(function () {
        Route::post('reverify', [UserController::class, 'reverify'])->name('reverify');

        Route::put('/', [UserController::class, 'update'])->name('update');
        Route::get('show', [UserController::class, 'show'])->name('show');
    });
});



