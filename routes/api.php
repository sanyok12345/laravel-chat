<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Updates\LongPollingController;
use App\Http\Controllers\Chats\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chats\ReactionController;
use \App\Http\Middleware\Api\EnsureApiTokenIsValid;

Route::middleware([EnsureApiTokenIsValid::class])->group(function () {
    //Messages
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);
    Route::delete('/messages', [ChatController::class, 'deleteMessage']);
    Route::patch('/messages', [ChatController::class, 'updateMessage']);

    //Chat default view
    Route::get('/chat', [ChatController::class, 'index']);

    //Reactions
    Route::get('/message/reactions', [ChatController::class, 'getMessageReactions']);
    Route::get('/message/reactions/count', [ChatController::class, 'getMessageReactionsCount']);
    Route::post('/message/reactions', [ChatController::class, 'reactToMessage']);
    Route::delete('/message/reactions', [ChatController::class, 'unreactToMessage']);
    Route::get('/reactions/names', [ReactionController::class, 'getReactionNames']);
    Route::post('/reactions', [ReactionController::class, 'addNewReaction']);

    //Promote user to admin
    Route::post('promote', [UserController::class, 'promote']);

    //Long polling
    Route::get('/long-poll/messages', [LongPollingController::class, 'checkMessages']);
});
