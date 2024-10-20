<?php

namespace App\Http\Middleware\Api;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->expectsJson()) {
            return $next($request);
        }

        // Retrieve the user by their API token
        $user = User::where('token', $request->header('api-token'))->first();

        // Check if user exists and the token matches
        if (!$user || $request->header('api-token') !== $user->token) {
            return response()->json([
                'message' => 'Unauthorized',
                'token' => $request->header('api-token'),
                'user' => $user,
                'Auth_check' => auth()->check(), // Use `auth()->check()` instead of `Auth::check()`
            ], 401);
        }

        // Pass the user information to the next middleware/handler
        return $next($request);
    }
}


