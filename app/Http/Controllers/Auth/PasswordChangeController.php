<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class PasswordChangeController extends Controller
{
    public function showChangeForm(): \Illuminate\View\View
    {
        return view('auth.passwords.change');
    }

    public function changePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request = validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, \Illuminate\Support\Facades\Auth::user()->password)) {
            return back()->withErrors(['old_password' => 'The provided password does not match our records.']);
        }

        \Illuminate\Support\Facades\Auth::user()->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ])->save();

        return redirect()->route('login')->with('status', 'Password changed successfully');
    }
}
