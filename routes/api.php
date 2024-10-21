<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Updates\LongPollingController;
use App\Http\Controllers\Chats\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chats\ReplyController;
use \App\Http\Middleware\Api\EnsureApiTokenIsValid;

Route::middleware([EnsureApiTokenIsValid::class])->group(function () {
    //Messages
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);
    Route::delete('/messages', [ChatController::class, 'deleteMessage']);
    Route::patch('/messages', [ChatController::class, 'updateMessage']);

    //Chat default view
    Route::get('/chat', [ChatController::class, 'index']);



    //Promote user to admin
    Route::post('promote', [UserController::class, 'promote']);

    //Long polling
    Route::post('/long-poll/messages', [LongPollingController::class, 'checkMessages']);
});
