<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle login request for regular users
     */
    public function login(Request $request)
    {
        \Log::info('Admin login attempt', ['email' => $request->email]);

        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($validated)) {
                \Log::warning('Admin login failed: Invalid credentials', ['email' => $request->email]);
                return response()->json([
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();

            if (!$user->is_admin) {
                \Log::warning('Non-admin user attempted admin login', ['email' => $user->email]);
                return response()->json([
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $token = $user->createToken('admin-token')->plainTextToken;

            \Log::info('Admin login successful', ['user_id' => $user->id]);

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin login error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);

            return response()->json([
                'message' => 'An error occurred during login'
            ], 500);
        }
    }

    /**
     * Handle registration request for regular users
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Create user with role "customer" by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Default role
        ]);

        Auth::guard('web')->login($user);
        // $request->session()->regenerate(); // HAPUS supaya tidak menendang session admin

        // Redirect ke URL yang tersimpan di session jika ada
        if ($request->has('redirect')) {
            return redirect()->to($request->redirect);
        } elseif (session()->has('redirect_url')) {
            $redirect = session()->get('redirect_url');
            session()->forget('redirect_url');
            return redirect()->to($redirect);
        }

        return redirect()->route('welcome');
    }

    /**
     * Handle logout request for regular users
     */
    public function logout(Request $request)
    {
        // Hanya logout guard 'web'
        Auth::guard('web')->logout();
        // $request->session()->invalidate(); // HAPUS supaya tidak menendang session admin
        // $request->session()->regenerateToken(); // HAPUS supaya tidak menendang session admin
        return redirect()->route('profileout');
    }

    /********************************************
     * Admin Authentication Methods
     ********************************************/

    /**
     * Show admin login form
     */
    public function showAdminLoginForm()
    {
        // If already logged in as admin, you can still show the login page
        // Optionally, display a message indicating they are already logged in
        if (Auth::guard('admin')->check()) {
            return view('auth.admin_login')->with('info', 'You are already logged in as an admin.');
        }

        return view('auth.admin_login');
    }

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

            // Validate request
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Find user and verify credentials
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                Log::warning('Admin login failed: User not found', ['email' => $validated['email']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                Log::warning('Admin login failed: Invalid password', ['email' => $validated['email']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Verify admin role
            if (!$user->isAdmin()) {
                Log::warning('Non-admin user attempted admin login', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Create token and login
            $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

            Log::info('Admin login successful', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Admin login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500);
        }
    }

    /**
     * Show admin registration form
     */
    public function showAdminRegistrationForm()
    {
        // Optional: Restrict access to existing admins
        // if (!Auth::guard('admin')->check()) {
        //     return redirect()->route('admin.login')
        //         ->withErrors(['auth' => 'You must be logged in as an admin to access this page']);
        // }

        return view('auth.admin_registrasi'); // Pastikan view ini ada
    }

    /**
     * Handle admin registration request
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Verify admin registration code
        if ($request->admin_code !== config('auth.admin_registration_code')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid admin registration code'
                ], 403);
            }
            return redirect()->back()
                ->withErrors(['admin_code' => 'Invalid admin registration code'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Create user with role "admin"
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Admin registered successfully',
                'user' => $user
            ], 201);
        }

        return redirect()->route('admin.login')
            ->with('success', 'Admin berhasil didaftarkan! Silakan login.');
    }

    /**
     * Handle admin logout request
     */
    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();
        // $request->session()->invalidate(); // HAPUS supaya tidak menendang session user
        // $request->session()->regenerateToken(); // HAPUS supaya tidak menendang session user
        return redirect()->route('admin.login');
    }
}
