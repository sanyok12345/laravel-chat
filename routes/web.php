<?php

use App\Http\Controllers\GroupChatController;
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
    Route::get('/group-chats/{id}/get-group', [GroupChatController::class, 'getGroupChatInfo']);
    Route::get('/group-chats/get-groups', [GroupChatController::class, 'getGroups']);
    Route::get('/group-chats/{id}/get-messages', [GroupChatController::class, 'getMessages']);
    Route::post('/group-chats/create-group', [GroupChatController::class, 'createGroup']);
    Route::post('/group-chats/{id}/send-message', [GroupChatController::class, 'sendMessage']);
});

require __DIR__.'/auth.php';
