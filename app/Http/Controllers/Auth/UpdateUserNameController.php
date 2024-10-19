<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateUserNameController extends Controller
{
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->update([
            'username' => $request->username,
        ]);

        return redirect()->route('profile.edit')->with('status', 'username-updated');
    }

    public function update(): \Illuminate\View\View
    {
        return view('auth.update-username');
    }
}
