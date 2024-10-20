<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = auth()->user();

        if (!$user || $request->header('api-token') !== $user->token) {
            return response()->json(['message' => 'Unauthorized', 'token' => $request->header('api-token')], 401);
        }

        return $next($request);
    }
}

