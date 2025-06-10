<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\AdminResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BaseAdminController extends Controller
{
    use AdminResponse;

    protected function validateAdmin()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return $this->errorResponse('Unauthorized. Admin privileges required.', null, 403);
        }

        return null;
    }

    protected function validate($data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        return null;
    }
}
