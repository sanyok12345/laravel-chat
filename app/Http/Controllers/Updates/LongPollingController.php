<?php

namespace App\Http\Controllers\Updates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;


class LongPollingController extends Controller
{
    public function checkMessages(Request $request): \Illuminate\Http\JsonResponse
    {
        $lastMessageId = $request->input('last_message_id'); // Get the last message ID received by the client
        $timeout = 30; // Timeout in seconds
        $startTime = time();

        // Polling loop (will check for new messages for up to 30 seconds)
        while (true) {
            // Check if there are new messages
            $newMessages = Message::where('id', '>', $lastMessageId)->get();

            if ($newMessages->count() > 0) {
                // If new messages are found, return them to the client
                return response()->json([
                    'new_messages' => $newMessages,
                ]);
            }

            // Break the loop after the timeout period (30 seconds)
            if (time() - $startTime > $timeout) {
                return response()->json([
                    'new_messages' => [],
                ]);
            }

            // Sleep for a short time to avoid overwhelming the server
            usleep(500000); // Sleep for 500ms
        }
    }
}
