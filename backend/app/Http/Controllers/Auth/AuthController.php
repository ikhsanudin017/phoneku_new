<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle login request for regular users
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Cek dulu apakah email ini terdaftar sebagai admin
        $user = User::where('email', $request->email)->first();
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.login')
                ->withErrors(['auth' => 'Akun ini adalah admin. Silakan login melalui halaman admin.']);
        }

        // Jika bukan admin, lanjutkan proses login user biasa
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            // $request->session()->regenerate(); // HAPUS supaya tidak menendang session admin

            // Redirect ke URL yang tersimpan di session jika ada
            if ($request->has('redirect')) {
                return redirect()->to($request->redirect);
            } elseif (session()->has('redirect_url')) {
                $redirect = session()->get('redirect_url');
                session()->forget('redirect_url');
                return redirect()->to($redirect);
            }

            return redirect()->intended(route('welcome'));
        }

        return redirect()->back()
            ->withErrors(['auth' => 'Email atau password salah'])
            ->withInput($request->except('password'));
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
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
                ->withInput($request->except('password'));
        }

        // Check if the user exists and has role 'admin'
        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            return redirect()->back()
                ->withErrors(['auth' => 'Invalid credentials'])
                ->withInput($request->except('password'));
        }

        if ($request->expectsJson()) {
            $token = $user->createToken('admin_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Admin login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        Auth::login($user, $request->has('remember'));
        return redirect()->intended(route('admin.dashboard'));

        return redirect()->back()
            ->withErrors(['auth' => 'Invalid credentials'])
            ->withInput($request->except('password'));
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
