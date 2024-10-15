<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function getGroups(){
        $groups = GroupChat::listAll();

        return response()->json($groups, 201);
    }
    public function create(Request $request)
    {
        $group = GroupChat::create(['name' => $request->name]);
        $group->users()->attach(auth()->id());

        return response()->json($group, 201);
    }
    public function addUserToGroupChat(Request $request, $groupChatId){
        $groupChat = GroupChat::findOrFail($groupChatId);
        $userId = $request->input('user_id');

        //Check if user already is in group
        if ($groupChat->users()->where('user_id',$userId)->exists()){
            return response()->json(['message' => 'User already is in this group!'],400);
        }
        //Attach the user to group chat
        $groupChat->users()->attach($userId);

        return response()->json(['message'=>'User added to group!'],200);
    }
    public function sendMessage(Request $request, $groupId)
    {
        $group = GroupChat::findOrFail($groupId);
        $message = $group->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

}
