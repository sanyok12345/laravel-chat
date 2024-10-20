<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // Utility method to fetch authenticated user by API token
    private function getAuthenticatedUser($apiToken)
    {
        return User::where('token', $apiToken)->first();
    }

    // Send a message
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'message' => 'required|string',
        ]);

        $user = $this->getAuthenticatedUser($request->apiToken);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('user', ['user in send message' => $user]);

        $message = Message::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json($message, 201);
    }

    // Retrieve all messages
    public function getMessages(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Message::all());
    }

    // Delete a message
    public function deleteMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $user = $this->getAuthenticatedUser($request->apiToken);

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

        $user = $this->getAuthenticatedUser($request->apiToken);

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

    // React to a message
    public function reactToMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'message_id' => 'required|integer',
            'reaction_id' => 'required|integer',
        ]);

        $user = $this->getAuthenticatedUser($request->apiToken);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = Message::find($request->message_id);
        $reaction = Reaction::find($request->reaction_id);

        if ($message && $reaction) {
            $message->reactions()->attach($reaction, ['user_id' => $user->id]);
            return response()->json($message->reactions);
        }

        return response()->json(['message' => 'Invalid message or reaction'], 400);
    }

    // Unreact from a message
    public function unreactToMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'message_id' => 'required|integer',
            'reaction_id' => 'required|integer',
        ]);

        $message = Message::find($request->message_id);
        $reaction = Reaction::find($request->reaction_id);

        if ($message && $reaction) {
            $message->reactions()->detach($reaction);
            return response()->json($message->reactions);
        }

        return response()->json(['message' => 'Invalid message or reaction'], 400);
    }

    // Get message reactions
    public function getMessageReactions(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'message_id' => 'required|integer',
        ]);

        $message = Message::find($request->message_id);

        return response()->json($message ? $message->reactions : []);
    }

    // Get the count of message reactions
    public function getMessageReactionsCount(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'message_id' => 'required|integer',
        ]);

        $message = Message::find($request->message_id);

        return response()->json($message ? $message->reactions->count() : 0);
    }

    // Return the chat index view
    public function index(): \Illuminate\View\View
    {
        $user = auth()->user();
        return view('chat.index', ['apiToken' => $user->token]);
    }
}
