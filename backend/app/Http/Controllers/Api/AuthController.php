<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle admin login request
     */
    public function adminLogin(Request $request)
    {
        try {
            Log::info('Admin login attempt started', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            // Validate request with stricter rules
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
                'remember' => ['boolean']
            ]);

            if ($validator->fails()) {
                Log::warning('Admin login validation failed', [
                    'email' => $request->email,
                    'errors' => $validator->errors()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for rate limiting using cache
            $ipKey = 'login_attempts_' . $request->ip();
            $emailKey = 'login_attempts_' . $request->email;
            
            $ipAttempts = cache()->get($ipKey, 0);
            $emailAttempts = cache()->get($emailKey, 0);

            if ($ipAttempts >= 5 || $emailAttempts >= 5) {
                Log::warning('Rate limit exceeded for admin login', [
                    'ip' => $request->ip(),
                    'email' => $request->email
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again later.'
                ], 429);
            }

            // Find the user
            $user = User::where('email', $request->email)->first();
            
            // Log additional debug info
            Log::debug('Admin login user lookup', [
                'email' => $request->email,
                'user_found' => (bool)$user,
                'user_role' => $user ? $user->role : null
            ]);

            if (!$user || !Hash::check($request->password, $user->password)) {
                // Increment attempt counters
                cache()->put($ipKey, $ipAttempts + 1, now()->addMinutes(15));
                cache()->put($emailKey, $emailAttempts + 1, now()->addMinutes(15));
                
                Log::warning('Admin login failed: Invalid credentials', [
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Ensure the user is active
            if (isset($user->status) && $user->status !== 'active') {
                Log::warning('Admin login failed: Account inactive', [
                    'email' => $request->email,
                    'user_id' => $user->id,
                    'status' => $user->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 403);
            }

            // Verify admin role
            if (!$user->isAdmin()) {
                Log::warning('Non-admin user attempted admin login', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Login successful, create token with admin abilities
            try {
                // Revoke any existing admin tokens
                $user->tokens()->where('name', 'admin-token')->delete();

                // Create new token with admin abilities
                $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

                // Update last login timestamp and clear any failed attempts
                $user->update([
                    'last_login_at' => now(),
                    'failed_login_attempts' => 0,
                    'locked_until' => null
                ]);

                // Prepare user data for response
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'last_login_at' => $user->last_login_at
                ];

                Log::info('Admin login successful', [
                    'user_id' => $user->id,
                    'token_created' => true
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $userData,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]);

            } catch (\Exception $e) {
                Log::error('Token creation error during admin login', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during login. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Admin login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login'
            ], 500);
        }
    }

    /**
     * Handle admin user data request
     */
    public function adminUser(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'last_login_at' => $user->last_login_at
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admin user data', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching user data'
            ], 500);
        }
    }

    /**
     * Handle admin logout
     */
    public function adminLogout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin logout error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during logout'
            ], 500);
        }
    }
}
