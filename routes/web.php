<?php

use App\Http\Controllers\Profiles\ProfileController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\UpdateEmailController;
use \App\Http\Controllers\Auth\UpdateUserNameController;
use \App\Http\Controllers\Auth\UpdatePasswordController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/update-password', [UpdatePasswordController::class, 'update'])->name('password.edit');
    Route::post('/profile/update-password', [UpdatePasswordController::class, 'store']);
    Route::get('/profile/update-email', [UpdateEmailController::class, 'update'])->name('email.edit');
    Route::post('/profile/update-email', [UpdateEmailController::class, 'store']);
    Route::get('/profile/change-username', [UpdateUserNameController::class, 'update'])->name('username.edit');
    Route::post('/profile/change-username', [UpdateUserNameController::class, 'store']);

});

require __DIR__ . '/auth.php';
