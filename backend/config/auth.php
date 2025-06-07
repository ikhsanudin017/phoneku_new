<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'conditions' => [
                'role' => 'customer',
            ],
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'conditions' => [
                'role' => 'admin',
            ],
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // Same model, but we'll filter by role in the guard
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'admins' => [ // Add a separate password reset configuration for admins
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    /*
    |--------------------------------------------------------------------------
    | Admin Registration Code
    |--------------------------------------------------------------------------
    |
    | This value is the secret code required to register new admin users.
    | Change this in your .env file for security.
    |
    */
    'admin_registration_code' => env('ADMIN_REGISTRATION_CODE', 'default-admin-code'),
];
