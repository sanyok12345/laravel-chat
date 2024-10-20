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
        $lastMessageId = $request->input('last_message_id'); // Get the last message ID received by the client
        $timeout = 4; // Timeout in seconds
        $startTime = time();

        if (!$lastMessageId) {
            $lastMessageId = 0;
        }

        // Polling loop (will check for new messages for up to 30 seconds)
        while (true) {
            // Check if there are new messages
            $newMessages = Message::where('id', '>', $lastMessageId)->get();

            if ($newMessages->count() > 0) {
                Log::info('New messages found',['new_messages' => $newMessages]);
                return response()->json(['new_messages' => $newMessages]);
            }

            // Break the loop after the timeout period
            if (time() - $startTime > $timeout) {
                Log::info('Polling timeout reached, no new messages');
                return response()->json([
                    'new_messages' => [],
                ]);
            }

            // Sleep for a short time to avoid overwhelming the server
            usleep(500000); // Sleep for 500ms
        }
    }
}
