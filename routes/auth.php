<?php

use App\Http\Controllers\Auth\UpdateEmailController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Auth\UpdateUserNameController;
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
    Route::get('/profile/update-password', [UpdatePasswordController::class, 'update'])->name('password.edit');
    Route::post('/profile/update-password', [UpdatePasswordController::class, 'store']);
    Route::get('/profile/update-email', [UpdateEmailController::class, 'update'])->name('email.edit');
    Route::post('/profile/update-email', [UpdateEmailController::class, 'store']);
    Route::get('/profile/change-username', [UpdateUserNameController::class, 'update'])->name('username.edit');
    Route::post('/profile/change-username', [UpdateUserNameController::class, 'store']);

    Route::post('logout', [UserController::class, 'logout'])
        ->name('logout');

});
