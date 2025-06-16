<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminChatController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CheckoutController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// CSRF token route
Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});

// User Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(['throttle:3,1'])
        ->name('user.register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(['throttle:5,1'])
        ->name('user.login');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware(['throttle:3,1'])
        ->name('user.forgot-password');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('user.reset-password');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('user.logout');
        Route::get('/user', [AuthController::class, 'user'])
            ->name('user.check');
    });
});

// Admin Authentication Routes (unchanged from original)
Route::prefix('auth/admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware(['throttle:5,1'])
        ->name('admin.login');

    Route::post('/register', [AdminAuthController::class, 'register'])
        ->middleware(['throttle:3,1'])
        ->name('admin.register');

    Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword'])
        ->middleware(['throttle:3,1'])
        ->name('admin.forgot-password');

    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
        ->name('admin.reset-password');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('admin.logout');
        Route::post('/refresh-token', [AdminAuthController::class, 'refreshToken'])
            ->name('admin.refresh-token');
        Route::get('/check', [AdminAuthController::class, 'check'])
            ->name('admin.check');
    });
});

// Admin Routes (unchanged from original)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats']);
    Route::get('/dashboard/charts', [AdminDashboardController::class, 'getCharts']);
    Route::get('/dashboard/recent-orders', [AdminDashboardController::class, 'getRecentOrders']);
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/user/{id}', [AdminUserController::class, 'show']);
    Route::get('/user', [AdminUserController::class, 'profile']);
    Route::post('/user', [AdminUserController::class, 'store']);
    Route::put('/user/{id}', [AdminUserController::class, 'update']);
    Route::delete('/user/{id}', [AdminUserController::class, 'destroy']);
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
    Route::get('/users/{user}/orders', [AdminUserController::class, 'getUserOrders']);
    Route::get('/profile', [AdminUserController::class, 'profile']);
    Route::put('/profile', [AdminUserController::class, 'updateProfile']);

    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/product/{id}', [AdminProductController::class, 'show']);
    Route::post('/product', [AdminProductController::class, 'store']);
    Route::post('/product/{id}', [AdminProductController::class, 'update']);
    Route::delete('/product/{id}', [AdminProductController::class, 'destroy']);
    Route::post('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus']);
    Route::post('/products/bulk-delete', [AdminProductController::class, 'bulkDelete']);

    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/order/{id}', [AdminOrderController::class, 'show']);
    Route::put('/order/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::apiResource('orders', AdminOrderController::class)->except(['destroy']);
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::get('/orders/{order}/items', [AdminOrderController::class, 'getOrderItems']);

    Route::get('/messages', [AdminChatController::class, 'index']);
    Route::get('/messages/{conversation}', [AdminChatController::class, 'show']);
    Route::post('/messages/{conversation}', [AdminChatController::class, 'sendMessage']);
    Route::get('/conversations', [AdminChatController::class, 'getConversations']);
});

// Public product routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Protected user routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'getProfile']);
        Route::put('/', [UserController::class, 'updateProfile']);
        Route::post('/email/request-otp', [UserController::class, 'requestEmailOtp']);
        Route::post('/email/verify-otp', [UserController::class, 'verifyEmailOtp']);
        Route::post('/phone/request-otp', [UserController::class, 'requestPhoneOtp']);
        Route::post('/phone/verify-otp', [UserController::class, 'verifyPhoneOtp']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add/{productId}', [CartController::class, 'add']);
        Route::put('/{id}', [CartController::class, 'updateQuantity']);
        Route::delete('/{id}', [CartController::class, 'remove']);
        Route::get('/count', [CartController::class, 'count']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    Route::prefix('chat')->group(function () {
        Route::get('/messages/{receiverId}', [ChatController::class, 'fetchMessages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
    });
});

// Fallback route for 404
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});