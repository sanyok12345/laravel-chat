<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    public function getMessages(): \Illuminate\Http\JsonResponse
    {
        $messages = Message::all();
        return response()->json($messages);
    }

    public function deleteMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->id);
        $this->authorize('delete', $message);
        $this->validate($request, [
            'id' => 'required|integer',
        ]);
        if ($message->user_id !== auth()->id() || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'You are not authorized to delete this message'], 403);
        } else {
            $message->delete();
        }

        return response()->json(null, 204);
    }

    public function updateMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->id);
        $this->authorize('update', $message);
        $this->validate($request, [
            'message' => 'required|string',
        ]);
        if ($message->user_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to update this message'], 403);
        } else {
            $message->update([
                'message' => $request->message,
            ]);
        }
        return response()->json($message);
    }

    public function reactToMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->message_id);
        $reaction = Reaction::find($request->reaction_id);
        $message->reactions()->attach($reaction, ['user_id' => auth()->id()]);
        return response()->json($message->reactions);
    }

    public function unreactToMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->message_id);
        $reaction = Reaction::find($request->reaction_id);
        $message->reactions()->detach($reaction);
        return response()->json($message->reactions);
    }

    public function getMessageReactions(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->message_id);
        return response()->json($message->reactions);
    }

    public function getMessageReactionsCount(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = Message::find($request->message_id);
        return response()->json($message->reactions->count());
    }

    public function index(): \Illuminate\View\View
    {
        return view('chat.index',['apiToken' => auth()->user()->token]);
    }

}
