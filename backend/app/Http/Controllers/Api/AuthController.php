<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Check if user is admin and prevent regular login
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini adalah admin. Silakan login melalui halaman admin.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Login admin
     */
    public function adminLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // First, find the user regardless of role
            $user = User::where('email', $request->email)->first();

            // User doesn't exist or password is wrong
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

        // Check if user is an admin after validating credentials
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // Invalidate any existing tokens
        $user->tokens()->delete();

        // Create new admin token with admin abilities
        $token = $user->createToken('admin_token', ['admin'])->plainTextToken;

        // Load profile relation if it exists
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Register admin
     */
    public function adminRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify admin registration code
        if ($request->admin_code !== config('auth.admin_registration_code')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid admin registration code'
            ], 403);
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Admin registered successfully',
            'user' => $admin,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::guard('web')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get user info
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Send forgot password email
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $resetToken = random_int(100000, 999999);

        // Store reset token (using otp field for simplicity)
        $user->update(['otp' => $resetToken]);

        // Send reset email
        try {
            \Mail::raw("Kode reset password Anda: $resetToken", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Reset Password PhoneKu');
            });

            return response()->json([
                'success' => true,
                'message' => 'Kode reset password telah dikirim ke email Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email reset password'
            ], 500);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->otp != $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Kode reset password salah'
            ], 400);
        }

        // Update password and clear token
        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }
}
