<?php

namespace App\Http\Controllers\Updates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class LongPollingController extends Controller
{
    public function checkMessages(Request $request): \Illuminate\Http\JsonResponse
    {

        $lastMessageId = $request->input('last_message_id', 0);
        $newMessages = Message::where('id', '>', $lastMessageId)->with([
            'user:id,name',               // Get the user who posted the message
            'parentMessage.user:id,name'  // Load the parent message (the message this one replies to) and its user
        ])->get();

        // Format the response
        $formattedMessages = $newMessages->map(function ($message) {
            // If the message is a reply, fetch the parent message
            $reply_to_message = null;

            if ($message->parentMessage) {
                $reply_to_message = [
                    'id' => $message->parentMessage->id,
                    'message' => $message->parentMessage->message,
                    'user' => $message->parentMessage->user,
                ];
            }

            return [
                'id' => $message->id,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'message' => $message->message,
                'reply_to_message' => $reply_to_message,  // Parent message (if this is a reply)
                'user' => $message->user,
            ];
        });

        return response()->json(['new_messages' => $formattedMessages]);
    }
}
