<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    // Utility method to fetch authenticated user by API token
    public function getAuthenticatedUser($apiToken)
    {
        if (is_null($apiToken)) {
            return response()->json([
                'message' => 'Unauthorized, token is null',
            ], 401);
        }
        return User::where('token', $apiToken)->first();
    }

    // Send a message
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->has('message') && $request->has('reply_to_message_id')) {
            return app('App\Http\Controllers\Chats\ReplyController')->sendReply($request);
        }

        try {
            $this->validate($request, [
                'message' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $user = $this->getAuthenticatedUser($request->header('api-token'));

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('user', ['user in send message' => $user]);

        $message = Message::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => null,
        ]);

        return response()->json($message, 201);
    }

    // Retrieve all messages
    // app/Http/Controllers/Chats/ChatController.php
// app/Http/Controllers/Chats/ChatController.php
    // app/Http/Controllers/Chats/ChatController.php
    public function getMessages(): \Illuminate\Http\JsonResponse
    {
        // Fetch all messages along with the user who posted them and the message they are replying to
        $messages = Message::with([
            'user:id,name',               // Get the user who posted the message
            'parentMessage.user:id,name'  // Load the parent message (the message this one replies to) and its user
        ])->get();

        // Format the response
        $formattedMessages = $messages->map(function ($message) {
            // If the message is a reply, fetch the parent message
            $reply_to_message = null;

            if ($message->parentMessage) {
                $reply_to_message = [
                    'id' => $message->parentMessage->id,
                    'text' => $message->parentMessage->message,
                    'user' => $message->parentMessage->user,
                ];
            }

            return [
                'id' => $message->id,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'text' => $message->message,
                'reply_to_message' => $reply_to_message,  // Parent message (if this is a reply)
                'user' => $message->user,
            ];
        });

        return response()->json($formattedMessages);
    }


    // Delete a message
    public function deleteMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $user = $this->getAuthenticatedUser($request->header('api-token'));

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = Message::find($request->id);

        if (!$message || ($message->user_id !== $user->id && $user->role !== 'admin')) {
            return response()->json(['message' => 'You are not authorized to delete this message'], 403);
        }

        $message->delete();

        return response()->json(null, 204);
    }

    // Update a message
    public function updateMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $user = $this->getAuthenticatedUser($request->header('api-token'));

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = Message::find($request->id);

        if (!$message || $message->user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorized to update this message'], 403);
        }

        $message->update([
            'message' => $request->message,
            'updated_at' => now(),
        ]);

        return response()->json($message);
    }

    // Return the chat index view
    public function index(): \Illuminate\View\View
    {
        $user = auth()->user();
        return view('chat.index', ['apiToken' => $user->token]);
    }
}
