<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Updates\LongPollingController;
use App\Http\Controllers\Chats\ChatController;
use App\Http\Middleware\Api\EnsureApiTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureApiTokenIsValid::class)->group(function () {
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);
    Route::delete('/messages', [ChatController::class, 'deleteMessage']);
    Route::patch('/messages', [ChatController::class, 'updateMessage']);
    Route::get('/chat', [ChatController::class, 'index']);

    Route::post('promote', [UserController::class, 'promote']);

    Route::get('/long-poll/messages', [LongPollingController::class, 'checkMessages']);
});
