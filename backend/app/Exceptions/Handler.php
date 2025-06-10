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
                    'message' => $e->getMessage()
                ];

                if ($e instanceof AuthenticationException) {
                    $status = 401;
                    $response['message'] = 'Unauthenticated';
                } elseif ($e instanceof ValidationException) {
                    $status = 422;
                    $response['errors'] = $e->errors();
                } elseif ($e instanceof NotFoundHttpException) {
                    $status = 404;
                    $response['message'] = 'Not Found';
                }

                return response()->json($response, $status);
            }

            return parent::render($request, $e);
        });
    }
}
