<?php


use App\Http\Controllers\GroupChatController;
use App\Http\Middleware\EnsureApiTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureApiTokenIsValid::class)->group(function () {
    Route::post('/group-chats/{id}/add-user', [GroupChatController::class, 'addUserToGroupChat']);
    Route::post('/group-chats/{id}/remove-user', [GroupChatController::class, 'removeUserFromGroupChat']);
    Route::post('/group-chats/create-group', [GroupChatController::class, 'createGroup']);
    Route::post('/group-chats/{id}/send-message', [GroupChatController::class, 'sendMessage']);

    Route::get('/group-chats/{id}/get-group', [GroupChatController::class, 'getGroupChatInfo']);
    Route::get('/group-chats/get-groups', [GroupChatController::class, 'getGroups']);
    Route::get('/group-chats/{id}/get-messages', [GroupChatController::class, 'getMessages']);
});
