<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Profiles\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $apiToken = auth()->user()->token;
        return view('chat', ['apiToken' => $apiToken]);
    }
    return view('login');
})->middleware(['auth', 'verified'])->name('chat');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/get-profile', [UserController::class, 'getMe'])->name('profile.get');
});

require __DIR__ . '/auth.php';
