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
        $apiToken = $request->header('api-token');

        // Check if the token is null
        if (is_null($apiToken)) {
            return response()->json([
                'message' => 'Unauthorized',
                'token' => $apiToken,
                'user' => null,
                'Auth_check' => auth()->check(),
            ], 401);
        }

        // Retrieve the user by their API token
        $user = User::where('token', $apiToken)->first();

        // Check if user exists and the token matches
        if (!$user || $apiToken !== $user->token) {
            return response()->json([
                'message' => 'Unauthorized after token check',
                'token' => $apiToken,
                'user' => $user,
                'Auth_check' => auth()->check(),
            ], 401);
        }

        // Pass the user information to the next middleware/handler
        return $next($request);
    }
}


