<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Chats\ChatController;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ReplyController extends ChatController
{
    // app/Http/Controllers/Chats/ReplyController.php
public function sendReply(Request $request): \Illuminate\Http\JsonResponse
{
    try {
        $this->validate($request, [
            'message' => 'required|string',
            'reply_to_message_id' => 'required|integer',
        ]);
    } catch (ValidationException $e) {
        return response()->json(['message' => $e->getMessage()], 400);
    }

    $user = $this->getAuthenticatedUser($request->header('api-token'));

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    Log::info('user', ['user in send message reply' => $user]);
    $message = Message::create([
        'user_id' => $user->id,
        'message' => $request->message,
        'reply_to' => $request->reply_to_message_id, // Ensure this field is set correctly
        'created_at' => now(),
        'updated_at' => null,
    ]);
    Log::info('message', ['message in send message reply' => $message]);

    return response()->json($message, 201);
}

    public function getReplies(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->validate($request, [
                'message_id' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $replies = MessageReply::where('message_id', $request->message_id)->get();

        return response()->json($replies);
    }

    public function deleteReply(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $reply = MessageReply::find($request->id);

        if (!$reply) {
            return response()->json(['message' => 'Reply not found'], 404);
        }

        $reply->delete();

        return response()->json(['message' => 'Reply deleted'], 200);
    }

    public function updateReply(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer',
                'reply' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $reply = MessageReply::find($request->id);

        if (!$reply) {
            return response()->json(['message' => 'Reply not found'], 404);
        }

        $reply->text = $request->reply;
        $reply->save();

        return response()->json($reply, 200);
    }
}
