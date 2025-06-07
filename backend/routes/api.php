<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
    Route::post('/admin/register', [AuthController::class, 'adminRegister']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
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

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/email', [UserController::class, 'updateEmail']);
        Route::put('/phone', [UserController::class, 'updatePhone']);
        Route::post('/email/request-otp', [UserController::class, 'requestEmailOtp']);
        Route::post('/email/verify-otp', [UserController::class, 'verifyEmailOtp']);
        Route::post('/phone/request-otp', [UserController::class, 'requestPhoneOtp']);
        Route::post('/phone/verify-otp', [UserController::class, 'verifyPhoneOtp']);
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

    // Customer specific routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/chat/customer', [ChatController::class, 'customerChat']);
    });

    // Admin specific routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/stats', [AdminController::class, 'stats']);

        // Product management
        Route::prefix('products')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });

        // User management
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminController::class, 'users']);
            Route::post('/', [AdminController::class, 'createUser']);
            Route::put('/{id}', [AdminController::class, 'updateUser']);
            Route::delete('/{id}', [AdminController::class, 'deleteUser']);
        });

        // Chat management
        Route::get('/chat', [ChatController::class, 'index']);
    });
});

// Fallback route for 404
Route::fallback(function(){
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});
