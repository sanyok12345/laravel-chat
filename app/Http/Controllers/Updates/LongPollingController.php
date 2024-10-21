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

        $newMessages = Message::where('id', '>', $lastMessageId)
            ->with('user:id,name')
            ->with('replies:id,message_id,user_id,reply')
            ->get();

        return response()->json(['new_messages' => $newMessages]);
    }
}
