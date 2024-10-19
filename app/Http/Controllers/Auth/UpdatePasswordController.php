<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

class UpdatePasswordController extends Controller
{
    public function update()
    {
        return view('auth.update-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'previous_password' => ['required', 'password'],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        if (!\Hash::check($request->previous_password, auth()->user()->password)) {
            return back()->withErrors(['previous_password' => 'The provided password does not match your current password']);
        } else {
            auth()->user()->update([
                'password' => bcrypt(request('password'))
            ]);
        }

        return response()->json(['message' => 'Password updated successfully']);
    }

}
