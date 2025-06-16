<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle user registration
     */
public function register(Request $request)
{
    try {
        Log::info('User registration attempt started', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree' => ['required', 'accepted']
        ], [
            'agree.accepted' => 'You must agree to the terms and conditions.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Validation passed, creating user');
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
            'email_verification_token' => Str::random(60)
        ]);

        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        // Definisikan $emailKey di sini untuk digunakan di seluruh method
        $emailKey = 'register_attempts_' . $request->email;
        cache()->forget($emailKey); // Hapus rate limit setelah sukses

        Log::info('User registration successful', ['user_id' => $user->id]);
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);

    } catch (\Exception $e) {
        $emailKey = 'register_attempts_' . $request->email;
        cache()->increment($emailKey, 1, now()->addMinutes(15));

        Log::error('User registration error', [
            'message' => $e->getMessage(),
            'email' => $request->email,
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing your request: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Handle user login
     */
public function login(Request $request)
{
    try {
        Log::info('User login attempt started', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['boolean']
        ]);

        if ($validator->fails()) {
            Log::warning('User login validation failed', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        // Rate limiting
        $ipKey = 'login_attempts_' . $request->ip();
        $emailKey = 'login_attempts_' . $request->email;
        
        $ipAttempts = cache()->get($ipKey, 0);
        $emailAttempts = cache()->get($emailKey, 0);

        if ($ipAttempts >= 5 || $emailAttempts >= 5) {
            Log::warning('Rate limit exceeded for user login', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'ip_attempts' => $ipAttempts,
                'email_attempts' => $emailAttempts
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            cache()->increment($ipKey, 1, now()->addMinutes(15));
            cache()->increment($emailKey, 1, now()->addMinutes(15));
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Invalid password attempt', ['email' => $request->email]);
            cache()->increment($ipKey, 1, now()->addMinutes(15));
            cache()->increment($emailKey, 1, now()->addMinutes(15));
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            Log::warning('User login failed: Account inactive', [
                'email' => $user->email,
                'user_id' => $user->id,
                'status' => $user->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        // Check if user is admin
        if ($user->isAdmin()) {
            Log::warning('Admin user attempted regular login', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Please use admin login page',
                'isAdmin' => true
            ], 403);
        }

        // Create token
        $user->tokens()->where('name', 'user-token')->delete();
        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        // Update user
        $user->update([
            'last_login_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        // Clear rate limit
        cache()->forget($ipKey);
        cache()->forget($emailKey);

        Log::info('User login successful', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_login_at' => $user->last_login_at
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);

    } catch (\Exception $e) {
        cache()->increment($ipKey, 1, now()->addMinutes(15));
        cache()->increment($emailKey, 1, now()->addMinutes(15));

        Log::error('User login error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred during login: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get authenticated user
     */
    public function user(Request $request)
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
            Log::error('Error fetching user data', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching user data'
            ], 500);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            Log::info('User logout successful', [
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('User logout error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during logout'
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Password changed successfully', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Password change error', [
                'error' => $e->getMessage()
            ]);

            
        }
    }
}