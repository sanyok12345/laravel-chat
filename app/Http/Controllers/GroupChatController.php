<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function getGroups(): \Illuminate\Http\JsonResponse
    {
        $groups = GroupChat::with('name')->get();
        return response()->json($groups, 201);
    }

    public function getGroupChatInfo(Request $request, $groupChatId): \Illuminate\Http\JsonResponse
    {
        $group = GroupChat::findOrFail($groupChatId);

        return response()->json($group);
    }

    public function createGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $userId = auth()->id(); // Get the authenticated user's ID

        $group = GroupChat::create([
            'name' => $request->name,
            'group_owner' => $userId, // Use the authenticated user ID
        ]);

        $group->members()->attach($userId); // Attach the owner to the group

        return response()->json($group, 201);
    }


    public function addUserToGroupChat(Request $request, $groupChatId): \Illuminate\Http\JsonResponse
    {
        $groupChat = GroupChat::findOrFail($groupChatId);
        $userId = $request->input('user_id');

        //Check if user already is in group
        if ($groupChat->members()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'User already is in this group!'], 400);
        }
        //Attach the user to group chat
        $groupChat->members()->attach($userId);

        return response()->json(['message' => 'User added to group!']);
    }

    public function removeUserFromGroupChat(Request $request, $groupChatId): \Illuminate\Http\JsonResponse
    {
        $groupChat = GroupChat::findOrFail($groupChatId);
        if ($groupChat->members()->where('user_id', $request->input('user_id'))->doesntExist()) {
            return response()->json(['message' => 'User is not in this group!'], 400);
        }
        if ($groupChat->members()->count() === 1) {
            return response()->json(['message' => 'You cannot remove the last user from the group!'], 400);
        }
        if ($groupChat->members()->where('user_id', auth()->id())->doesntExist()) {
            return response()->json(['message' => 'You are not allowed to remove users from this group!'], 400);
        }

        $userId = $request->input('user_id');

        if ($groupChat->members()->where('user_id', auth()->id())->wherePivot('group_owner', 1)->exists()) {
            // Admin (Group Owner) can remove any user
            $groupChat->members()->detach($userId);
        } elseif (auth()->id() === $userId) {
            // The user can only remove themselves
            $groupChat->members()->detach($userId);
        } else {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        return response()->json(['message' => 'User removed from group!']);
    }

    public function sendMessage(Request $request, $groupId): \Illuminate\Http\JsonResponse
    {
        $group = GroupChat::findOrFail($groupId);
        $message = $group->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    public function getMessages($groupId)
    {
        $group = GroupChat::findOrFail($groupId);
        $messages = $group->messages->get();
        return response()->json($messages);
    }

}
