<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Message;
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

    public function index(): \Illuminate\View\View
    {
        return view('chat.index');
    }

}
