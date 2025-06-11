<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Convert all exceptions to JSON response for API
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $status = 500;
                $response = [
                    'success' => false,
                    'message' => 'An error occurred while processing your request.'
                ];

                if ($e instanceof AuthenticationException) {
                    $status = 401;
                    $response['message'] = 'Session expired or invalid. Please login again.';
                    $response['code'] = 'AUTH_ERROR';
                } elseif ($e instanceof ValidationException) {
                    $status = 422;
                    $response['message'] = 'The given data was invalid.';
                    $response['errors'] = $e->errors();
                    $response['code'] = 'VALIDATION_ERROR';
                } elseif ($e instanceof NotFoundHttpException) {
                    $status = 404;
                    $response['message'] = 'The requested resource was not found.';
                    $response['code'] = 'NOT_FOUND';
                } elseif ($e instanceof \Illuminate\Database\QueryException) {
                    $status = 500;
                    $response['message'] = 'A database error occurred.';
                    $response['code'] = 'DB_ERROR';
                    \Log::error('Database error: ' . $e->getMessage());
                } elseif ($e instanceof \Exception) {
                    \Log::error('Unhandled exception: ' . $e->getMessage());
                }

                return response()->json($response, $status);
            }

            return parent::render($request, $e);
        });
    }
}
