<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class UpdateEmailController extends Controller
{
    public function update()
    {
        return view('auth.update-email');
    }

    public function store()
    {
        request()->validate([
            'email' => ['required', 'email', 'unique:users,email']
        ]);

        auth()->user()->update([
            'email' => request('email')
        ]);

        return response()->json(['message' => 'Email updated successfully']);
    }

}
