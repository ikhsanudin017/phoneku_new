<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // For API requests
        if ($request->expectsJson()) {
            if (!$request->user() || $request->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin privileges required.'
                ], 403);
            }
            return $next($request);
        }

        // For web requests
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['auth' => 'You don\'t have admin privileges']);
        }

        return $next($request);
    }
}
