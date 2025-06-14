<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Sanctum routes
        'sanctum/csrf-cookie',
        // Auth routes
        'api/auth/*',
        'api/login',
        'api/register',
        // Admin routes
        'api/admin/*'
    ];
}
