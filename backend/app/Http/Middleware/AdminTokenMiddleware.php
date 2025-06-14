<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            Log::warning('Unauthenticated access attempt to admin route', [
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$user->isAdmin()) {
            Log::warning('Unauthorized access attempt to admin route', [
                'user_id' => $user->id,
                'role' => $user->role,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Check if user status is active
        if ($user->status !== 'active') {
            Log::warning('Inactive admin attempted to access route', [
                'user_id' => $user->id,
                'status' => $user->status,
                'path' => $request->path()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        // Check if token has admin ability
        if (!$user->tokenCan('admin')) {
            Log::warning('Invalid admin token used', [
                'user_id' => $user->id,
                'token_abilities' => $request->bearerToken()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid admin token'
            ], 401);
        }

        return $next($request);
    }
}
