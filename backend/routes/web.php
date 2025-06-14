<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Since we're using Vue.js for the frontend, this file only handles
| server-side rendering (SSR) if needed. All other routes should go
| through the API routes.
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/tim', function () {
    return view('Home/tim');
})->name('tim');
Route::get('/aboutus', function () {
    return view('home.aboutus');
})->name('aboutus');
Route::get('/product/{product}', [HomeController::class, 'showProduct'])->name('product.show');
Route::get('/allproduct', [HomeController::class, 'allProducts'])->name('allproduct');
Route::get('/kontak', function () {
    return view('home.kontak');
})->name('kontak');


// Authentication routes (guest only)
Route::middleware(['guest:web'])->group(function () {
    Route::get('/login', function () {
        return view('Auth/login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/registrasi', function () {
        return view('Auth/registrasi');
    })->name('registrasi');
    Route::post('/registrasi', [AuthController::class, 'register'])->name('registrasi.post');
    Route::get('/lupa_password', function () {
        return view('Auth/lupapassword');
    })->name('lupa_password'); // Pastikan view ini ada
});

// Authenticated routes
Route::middleware(['auth:web'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/riwayatbeli', [ProfileController::class, 'riwayat'])->name('riwayatbeli');
    Route::get('/profilekeamanan', [ProfileController::class, 'privasiKeamanan'])->name('profilekeamanan');

    // Profile - ubah email
    Route::get('/ubah_email', [ProfileController::class, 'ubahEmail'])->name('ubah_email');
    Route::get('/ubah_email_otp', [ProfileController::class, 'ubahEmailOTP'])->name('ubah_email_otp');

    // Form ubah email - kirim OTP ke email lama
    Route::post('/kirim_otp_email_lama', [ProfileController::class, 'kirimOtpEmailLama'])->name('kirim_otp_email_lama');
    // Verifikasi OTP dan ubah email
    Route::post('/verifikasi_otp_ubah_email', [ProfileController::class, 'verifikasiOtpUbahEmail'])->name('verifikasi_otp_ubah_email');

    // Profile - ubah nomer telepon
    Route::get('/ubah_no_tlp', [ProfileController::class, 'tambahNoTelepon'])->name('ubah_no_tlp');
    Route::get('/ubah_no_tlp_otp', [ProfileController::class, 'tambahNoTeleponOTP'])->name('ubah_no_tlp_otp');

    // Form tambah/ubah nomer telepon - kirim OTP ke email lama
    Route::post('/kirim_otp', [ProfileController::class, 'kirimOtpAturNotlp'])->name('kirim_otp');
    // Verifikasi OTP dan tambah/ubah nomer telepon
    Route::post('/verifikasi_otp', [ProfileController::class, 'verifikasiOtpAturNoTlp'])->name('verifikasi_otp');

    // Cart and checkout
    Route::post('/cart/add/{productId}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

    Route::get('/riwayatpembelian', function () {
        return view('profile/riwayat_pembelian');
    })->name('riwayatpembelian');


    Route::get('/customer_support', [ChatController::class, 'customerChat'])->name('customer_support');

    // Add routes for customers to send and fetch messages
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{receiverId}', [ChatController::class, 'fetchMessages'])->name('chat.messages');

    // Logout


});

// Route untuk menampilkan halaman konfirmasi logout
Route::get('/logout', function () {
    return view('profile.logout');
})->name('profile.logout');

// User logout (POST)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk menampilkan halaman setelah keluar
Route::get('/setelah_keluar', function () {
    return view('profile.setelah_keluar');
})->name('profileout');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Public admin routes (for guests)
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin'])->name('login.post');
        Route::get('/register', [AuthController::class, 'showAdminRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'adminRegister'])->name('register.post');
        Route::get('/forgot-password', [AuthController::class, 'showAdminForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'adminForgotPassword'])->name('password.email');
        Route::get('/reset-password/{token}', [AuthController::class, 'showAdminResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'adminResetPassword'])->name('password.update');
    });

    // Protected admin routes
    Route::middleware(['auth', 'admin'])->group(function() {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        // Products management
        Route::prefix('products')->name('products.')->group(function() {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/preview', [ProductController::class, 'preview'])->name('preview');
        });

        // Users management
        Route::prefix('users')->name('users.')->group(function() {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Orders management
        Route::prefix('orders')->name('orders.')->group(function() {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('status.update');
        });

        // Chat management
        Route::prefix('chat')->name('chat.')->group(function() {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/messages/{receiverId}', [ChatController::class, 'fetchMessages'])->name('messages');
            Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        });

        // Profile & Logout
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
    });
});

// Pusher authentication (Jika menggunakan Websockets)
Route::post('/pusher/auth', function (Request $request) {
    $pusher = new Pusher\Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        ['cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => true]
    );
    return $pusher->socket_auth($request->channel_name, $request->socket_id);
})->middleware('auth:web,admin');
