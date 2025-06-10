<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return $this->unauthorizedResponse($request);
        }

        // Check if user is admin
        if ($user->role !== 'admin') {
            return $this->unauthorizedResponse($request);
        }

        // For API requests, ensure proper token abilities
        if ($request->expectsJson()) {
            $token = $request->bearerToken();
            if ($token) {
                $tokenModel = $user->tokens()->where('token', hash('sha256', $token))->first();
                if (!$tokenModel || !in_array('admin', $tokenModel->abilities)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid admin token.'
                    ], 403);
                }
            }
        }

        return $next($request);
    }

    private function unauthorizedResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin privileges required.'
            ], 403);
        }

        // For web requests
        return redirect()->route('admin.login')
            ->with('error', 'Admin privileges required.');
    }
}
