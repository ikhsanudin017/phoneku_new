<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\AdminResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BaseAdminController extends Controller
{
    use AdminResponse;

    /**
     * Validate if the current user is an admin
     */
    protected function validateAdmin()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user ? $user->id : null,
                'role' => $user ? $user->role : 'none'
            ]);
            return $this->unauthorizedResponse('Unauthorized. Admin privileges required.');
        }

        if (!$user->isActive()) {
            Log::warning('Inactive admin account access attempt', [
                'user_id' => $user->id
            ]);
            return $this->forbiddenResponse('Your admin account is not active.');
        }

        return null;
    }

    /**
     * Validate request data
     */
    protected function validateRequest($data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        return null;
    }

    /**
     * Log admin action
     */
    protected function logAdminAction($action, $details = [])
    {
        $user = Auth::user();
        Log::channel('admin')->info($action, array_merge([
            'admin_id' => $user->id,
            'admin_email' => $user->email
        ], $details));
    }
}
