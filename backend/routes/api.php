<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ChatController;

// Public Auth Routes
Route::prefix('auth')->group(function () {
    // User authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);

    // Admin authentication
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin']);
        Route::post('/register', [AuthController::class, 'adminRegister']);
        Route::post('/forgot-password', [AuthController::class, 'adminForgotPassword'])->middleware('throttle:6,1');
        Route::post('/reset-password', [AuthController::class, 'adminResetPassword'])->middleware('throttle:6,1');
    });
});

// Public product routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile management
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'getProfile']);
        Route::put('/', [UserController::class, 'updateProfile']);
        Route::post('/email/request-otp', [UserController::class, 'requestEmailOtp']);
        Route::post('/email/verify-otp', [UserController::class, 'verifyEmailOtp']);
        Route::post('/phone/request-otp', [UserController::class, 'requestPhoneOtp']);
        Route::post('/phone/verify-otp', [UserController::class, 'verifyPhoneOtp']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Cart routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add/{productId}', [CartController::class, 'add']);
        Route::put('/{id}', [CartController::class, 'updateQuantity']);
        Route::delete('/{id}', [CartController::class, 'remove']);
        Route::get('/count', [CartController::class, 'count']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    // Chat routes
    Route::prefix('chat')->group(function () {
        Route::get('/messages/{receiverId}', [ChatController::class, 'fetchMessages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
    });

    // Admin routes
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
        Route::get('/dashboard/recent-orders', [AdminDashboardController::class, 'recentOrders']);
        Route::get('/dashboard/popular-products', [AdminDashboardController::class, 'popularProducts']);

        // Users management
        Route::apiResource('users', AdminUserController::class);

        // Products management
        Route::apiResource('products', AdminProductController::class);

        // Orders management
        Route::get('/orders', [AdminController::class, 'orders']);
        Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus']);

        // Admin chat
        Route::get('/chats', [ChatController::class, 'adminChats']);
        Route::get('/chats/{user}/messages', [ChatController::class, 'adminMessages']);
    });
});

// Fallback route for 404
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});
