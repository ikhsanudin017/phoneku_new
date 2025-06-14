<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class ValidateAdminToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                Log::warning('Admin API access attempted without token');
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided',
                    'code' => 'TOKEN_MISSING'
                ], 401);
            }

            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                Log::warning('Invalid admin token used', ['token' => substr($token, 0, 10) . '...']);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                    'code' => 'TOKEN_INVALID'
                ], 401);
            }

            if (!$accessToken->can('admin') || $accessToken->tokenable->role !== 'admin') {
                Log::warning('Non-admin token used for admin API', [
                    'user_id' => $accessToken->tokenable_id,
                    'role' => $accessToken->tokenable->role ?? 'unknown'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient privileges',
                    'code' => 'INSUFFICIENT_PRIVILEGES'
                ], 403);
            }

            // Set user for the request
            $request->setUserResolver(function () use ($accessToken) {
                return $accessToken->tokenable;
            });

            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error in admin token validation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication error',
                'code' => 'AUTH_ERROR'
            ], 500);
        }
    }
}
