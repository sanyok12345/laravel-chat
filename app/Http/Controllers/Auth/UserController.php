<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request): \Illuminate\Http\RedirectResponse
    {
        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard');
    }

    public function promote(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find($request->id);
        $user->role = 'admin';
        $user->save();
        return redirect()->route('dashboard');
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        return redirect()->route('home');
    }


    public function showRegistrationForm(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function showLoginForm(): \Illuminate\View\View
    {
        return view('auth.login');
    }

}
