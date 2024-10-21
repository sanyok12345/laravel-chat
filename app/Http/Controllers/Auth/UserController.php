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
        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors([
                'email' => 'The email has already been taken.',
            ]);
        }

        if (User::where('username', $request->username)->exists()) {
            return back()->withErrors([
                'username' => 'The username has already been taken.',
            ]);
        }

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => bin2hex(random_bytes(32)),
        ]);

        Auth::login($user);

        $apiToken = $user->token;

        return redirect()->route('chat');

    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = auth()->user();
            $user->token = bin2hex(random_bytes(32));
            $user->save();
            Auth::login($user);
            return redirect()->route('chat')->with('apiToken', $user->token);
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function promote(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find($request->id);
        $user->role = 'admin';
        $user->save();
        return redirect()->route('chat');
    }


    public function logout(): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function getMe(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user()->only('id', 'name', 'username'));
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
