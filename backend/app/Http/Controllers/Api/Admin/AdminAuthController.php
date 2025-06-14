<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        try {
            Log::info('Admin login attempt', [
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

            // Find the user
            $user = User::where('email', $request->email)->first();

            // Check credentials
            if (!$user || !Hash::check($request->password, $user->password)) {
                $this->incrementFailedAttempts($user);
                
                Log::warning('Admin login failed: Invalid credentials', [
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials'],
                ]);
            }

            // Check if account is locked
            if ($user->locked_until && $user->locked_until->isFuture()) {
                $minutes = now()->diffInMinutes($user->locked_until);
                return response()->json([
                    'success' => false,
                    'message' => "Account is locked. Try again in {$minutes} minutes."
                ], 423);
            }

            // Check if user is admin
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

            // Check if account is active
            if ($user->status !== 'active') {
                Log::warning('Inactive admin attempted login', [
                    'user_id' => $user->id,
                    'status' => $user->status
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 403);
            }

            // Hapus token lama
            $user->tokens()->delete();
            
            // Buat token baru
            $token = $user->createToken('admin-token')->plainTextToken;

            // Reset failed attempts and update last login
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'last_login_at' => now()
            ]);

            Log::info('Admin login successful', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'last_login_at' => $user->last_login_at
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 401);
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
     * Handle admin registration
     */
    public function register(Request $request)
    {        Log::info('Admin registration attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'session' => [
                'id' => session()->getId(),
                'has_token' => session()->token() !== null,
            ],
            'csrf' => [
                'token_in_header' => $request->header('X-CSRF-TOKEN'),
                'token_in_input' => $request->input('_token'),
                'valid' => $request->hasValidSignature(),
            ],
            'cookies' => $request->cookies->all()
        ]);

        try {
            DB::beginTransaction();

            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'admin_code' => ['required', 'string']
            ]);

            if ($validator->fails()) {
                Log::warning('Admin registration validation failed', [
                    'email' => $request->email,
                    'errors' => $validator->errors()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify admin registration code
            $validCode = config('auth.admin_registration_code', env('ADMIN_REGISTRATION_CODE'));
            
            if (!$validCode || $request->admin_code !== $validCode) {
                Log::warning('Invalid admin registration code used', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'code_provided' => $request->admin_code
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid registration code'
                ], 403);
            }

            // Create admin user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now()
            ]);

            DB::commit();

            Log::info('Admin registration successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status
                ]
            ], 201);        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Admin registration error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
                'headers' => $request->headers->all(),
                'validation_errors' => $validator->errors() ?? null,
            ]);            $statusCode = 500;
            $errorMessage = 'An error occurred during registration';

            // Handle specific error cases
            if ($e instanceof \Illuminate\Database\QueryException) {
                if (str_contains($e->getMessage(), 'email_verified_at')) {
                    $errorMessage = 'Database schema error. Please contact support.';
                } elseif (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                    $errorMessage = 'Email address is already in use';
                    $statusCode = 422;
                } else {
                    $errorMessage = 'Database error occurred';
                }
            } elseif ($e instanceof \Illuminate\Session\TokenMismatchException) {
                $errorMessage = 'CSRF token mismatch. Please refresh the page and try again.';
                $statusCode = 419;
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], $statusCode);
        }
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            Log::info('Admin logout successful', [
                'user_id' => $request->user()->id
            ]);
            
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
                'message' => 'An error occurred during logout'
            ], 500);
        }
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'exists:users,email']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Verify user is an admin
            $user = User::where('email', $request->email)->first();
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is not registered as an admin'
                ], 403);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status !== Password::RESET_LINK_SENT) {
                throw new \Exception(__($status));
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin forgot password error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset link'
            ], 500);
        }
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60)
                    ])->save();
                }
            );

            if ($status !== Password::PASSWORD_RESET) {
                throw new \Exception(__($status));
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin password reset error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 500);
        }
    }

    /**
     * Check authentication status
     */    public function check(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }

            if (!$user->isAdmin()) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Not authorized as admin'
                ], 403);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Account is not active'
                ], 403);
            }

            if ($user->isLocked()) {
                $minutes = now()->diffInMinutes($user->locked_until);
                return response()->json([
                    'authenticated' => false,
                    'message' => "Account is locked. Try again in {$minutes} minutes."
                ], 423);
            }

            // User is authenticated and authorized
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'last_login' => $user->last_login_at?->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Auth check error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'authenticated' => false,
                'message' => 'Server error during authentication check'
            ], 500);
        }
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts(?User $user)
    {
        if (!$user) return;

        $user->failed_login_attempts++;
        
        // Lock account after 5 failed attempts
        if ($user->failed_login_attempts >= 5) {
            $user->locked_until = now()->addMinutes(15);
        }
        
        $user->save();
    }
}
