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
        $formattedMessages = $newMessages->map->formatMessage()->sortBy('id', SORT_REGULAR, false);;

        return response()->json(['new_messages' => $formattedMessages]);
    }
}
