<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class AdminAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse($request);
        }

        $user = Auth::user();

        // Check if user is admin
        if ($user->role !== 'admin') {
            return $this->unauthorizedResponse($request);
        }

        // Check account status
        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact the administrator.',
                'code' => 'ACCOUNT_INACTIVE'
            ], 403);
        }

        // Update last activity in the database directly
        User::where('id', $user->id)->update([
            'last_activity' => Carbon::now()
        ]);

        return $next($request);
    }

    private function unauthorizedResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
                'code' => 'UNAUTHORIZED'
            ], 401);
        }

        return redirect()->route('admin.login');
    }
}
