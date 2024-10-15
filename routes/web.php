<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/group-chats/{id}/add-user', [GroupChatController::class, 'addUserToGroupChat']);
    Route::post('/group-chats/{id}/remove-user', [GroupChatController::class, 'removeUserFromGroupChat']);
    Route::get('/group-chats/{id}/get-users-in-group', [GroupChatController::class, 'getUsersInGroup']);
    Route::get('/group-chats/get-groups', [GroupChatController::class, 'getGroups']);
    Route::post('/group-chats/create', [GroupChatController::class, 'create']);
    Route::post('/group-chats/send-message', [GroupChatController::class, 'sendMessage']);
});

require __DIR__.'/auth.php';
