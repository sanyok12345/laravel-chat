<?php

use App\Http\Controllers\Auth\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [UserController::class, 'showRegistrationForm'])
        ->name('register');
    Route::post('register', [UserController::class, 'register']);

    Route::get('login', [UserController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [UserController::class, 'login']);

    Route::get('forgot-password', [UserController::class, 'showForgotForm'])
        ->name('password.request');
    Route::post('forgot-password', [UserController::class, 'sendResetLinkEmail']);

    Route::get('reset-password/{token}', [UserController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('reset-password', [UserController::class, 'resetPassword']);

});
Route::middleware('auth')->group(function () {
    Route::get('change-password', [UserController::class, 'showChangeForm'])
        ->name('password.change');
    Route::post('change-password', [UserController::class, 'changePassword']);

    Route::post('logout', [UserController::class, 'logout'])
        ->name('logout');

});
