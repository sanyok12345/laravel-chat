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

        return response()->json($group, 200);
    }

    public function createGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        $group = GroupChat::create(['name' => $request->name], ['group_owner' => auth()->id()]);
        $group->users()->attach(auth()->id());

        return response()->json($group, 201);
    }

    public function addUserToGroupChat(Request $request, $groupChatId): \Illuminate\Http\JsonResponse
    {
        $groupChat = GroupChat::findOrFail($groupChatId);
        $userId = $request->input('user_id');

        //Check if user already is in group
        if ($groupChat->users()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'User already is in this group!'], 400);
        }
        //Attach the user to group chat
        $groupChat->users()->attach($userId);

        return response()->json(['message' => 'User added to group!'], 200);
    }

    public function removeUserFromGroupChat(Request $request, $groupChatId): \Illuminate\Http\JsonResponse
    {
        $groupChat = GroupChat::findOrFail($groupChatId);
        if ($groupChat->users()->where('user_id', $request->input('user_id'))->doesntExist()) {
            return response()->json(['message' => 'User is not in this group!'], 400);
        }
        if ($groupChat->users()->count() === 1) {
            return response()->json(['message' => 'You cannot remove the last user from the group!'], 400);
        }
        if ($groupChat->users()->where('user_id', auth()->id())->doesntExist()) {
            return response()->json(['message' => 'You are not allowed to remove users from this group!'], 400);
        }

        $userId = $request->input('user_id');

        if ($groupChat->users()->where('user_id', auth()->id())->wherePivot('group_owner', 1)->exists()) {
            // Admin (Group Owner) can remove any user
            $groupChat->users()->detach($userId);
        } elseif (auth()->id() === $userId) {
            // The user can only remove themselves
            $groupChat->users()->detach($userId);
        } else {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        return response()->json(['message' => 'User removed from group!'], 200);
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
        return response()->json($messages, 200);
    }

}
