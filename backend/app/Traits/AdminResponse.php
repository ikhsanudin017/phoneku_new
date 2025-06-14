<?php

namespace App\Traits;

trait AdminResponse
{
    /**
     * Format success response
     */
    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Format error response
     */
    protected function errorResponse($message = 'Error', $errors = null, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Format paginated response
     */
    protected function paginatedResponse($data, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total()
            ]
        ]);
    }

    /**
     * Format validation error response
     */
    protected function validationErrorResponse($errors)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }

    /**
     * Format unauthorized response
     */
    protected function unauthorizedResponse($message = 'Unauthorized access')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => 'UNAUTHORIZED'
        ], 401);
    }

    /**
     * Format forbidden response
     */
    protected function forbiddenResponse($message = 'Access forbidden')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => 'FORBIDDEN'
        ], 403);
    }
}
