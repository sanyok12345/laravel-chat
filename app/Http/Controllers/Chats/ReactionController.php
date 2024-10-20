<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function getReactionNames(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(Reaction::all()->pluck('name', 'id'));
    }

    public function addNewReaction(Request $request): \Illuminate\Http\JsonResponse
    {
        $path_to_reactions = public_path('images/reactions');
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:png,svg|max:2048',
        ]);

        if (!file_exists($path_to_reactions)) {
            mkdir($path_to_reactions, 0777, true);
        }

        $imageName = $request->name . '.' . $request->image->extension();

        $request->image->move($path_to_reactions, $imageName);


        $reaction = Reaction::create([
            'name' => $request->name,
            'path' => $path_to_reactions . '/' . $imageName,
        ]);
        return response()->json($reaction, 201);
    }
}
