<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthentication
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

        // Check if user exists and is admin
        if (!$user || !$user->isAdmin()) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user?->id,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            return $this->unauthorizedResponse($request);
        }

        // Check if account is active
        if ($user->status !== 'active') {
            Log::warning('Inactive admin attempted access', [
                'user_id' => $user->id,
                'status' => $user->status,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
                'code' => 'ACCOUNT_INACTIVE'
            ], 403);
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            
            return response()->json([
                'success' => false,
                'message' => "Account is locked. Try again in {$minutes} minutes.",
                'code' => 'ACCOUNT_LOCKED'
            ], 423);
        }

        // Add admin info to request for controllers
        $request->attributes->add(['admin_user' => $user]);

        return $next($request);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
                'code' => 'UNAUTHORIZED'
            ], 401);
        }

        return redirect()->route('admin.login')->withErrors([
            'auth' => 'Please login as an administrator to access this area.'
        ]);
    }
}
