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

        if (!$user) {
            return $this->unauthorizedResponse($request);
        }

        if ($user->role !== 'admin') {
            return $this->unauthorizedResponse($request);
        }

        // Check if user status is active
        if (isset($user->status) && $user->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun admin tidak aktif.'
                ], 403);
            }
            return redirect()->route('admin.login')
                ->with('error', 'Akun admin tidak aktif.');
        }

        // For API requests, ensure proper token abilities
        if ($request->expectsJson()) {
            $token = $request->bearerToken();
            if ($token) {
                $tokenModel = $user->tokens()->where('token', hash('sha256', $token))->first();
                if (!$tokenModel || !in_array('admin', $tokenModel->abilities)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token admin tidak valid.'
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
                'message' => 'Unauthorized. Dibutuhkan hak akses admin.'
            ], 403);
        }

        return redirect()->route('admin.login')
            ->with('error', 'Dibutuhkan hak akses admin.');
    }
}
