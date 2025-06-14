<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Registration Code
    |--------------------------------------------------------------------------
    |
    | This value is the secret code required to register new admin users.
    | Change this in your .env file for security.
    |
    */
    'admin_registration_code' => env('ADMIN_REGISTRATION_CODE', null),

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
        'admin-api' => [
            'driver' => 'sanctum',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'conditions' => [
                'role' => 'admin',
            ],
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
    | Token Lifetimes
    |--------------------------------------------------------------------------
    |
    | Here you may specify the amount of time password reset tokens,
    | verification tokens, etc will be considered valid.
    |
    */
    'verification_token_expire' => 60, // minutes
    'reset_token_expire' => 60,   // minutes
    'auth_token_expire' => 24 * 60, // minutes (24 hours)
];
