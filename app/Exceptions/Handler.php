<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        //Handle validation errors
        if ($exception instanceof ValidationException) {
            return $this->errorResponse(
                'Validation failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $exception->errors()
            );
        }

        //Handle not found routes / models
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('Resource not found', Response::HTTP_NOT_FOUND);
        }

        //Handle api request generic server errors
        if ($request->expectsJson()) {
            return $this->errorResponse(
                $exception->getMessage() ?: 'Something went wrong',
                method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        //Handle Jwt errors
        if ($exception instanceof TokenExpiredException) {
            return $this->errorResponse('Token has expired. Please login again.', Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof TokenInvalidException) {
            return $this->errorResponse('Invalid token. Please login again.', Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof JWTException) {
            return $this->errorResponse('Token not provided or invalid.', Response::HTTP_UNAUTHORIZED);
        }

        //Handle default route for non-api route
        return parent::render($request, $exception);
    }
}
