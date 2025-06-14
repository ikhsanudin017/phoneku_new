<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        if(!$response) {
            $response = response('', 204);
        }

        $headers = [
            'Access-Control-Allow-Origin' => $request->header('Origin'),
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-CSRF-TOKEN, X-XSRF-TOKEN',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers' => 'X-CSRF-TOKEN, X-XSRF-TOKEN',
            'Access-Control-Max-Age' => '7200'
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(200);
        }

        return $response;
    }
}
