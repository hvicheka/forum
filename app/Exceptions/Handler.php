<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use ParseError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return response([
                    'status' => 'error',
                    'errors' => $exception->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($exception instanceof AuthorizationException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof ModelNotFoundException ||
                $exception instanceof NotFoundHttpException
            ) {
                return response([
                    'status' => 'error',
                    'message' => '404 Not Found.'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof QueryException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof ParseError) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof BindingResolutionException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof \BadMethodCallException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 401);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
            if ($exception instanceof \ArgumentCountError) {
                return response([
                    'status' => 'error',
                    'message' => 'Too few arguments to function'
                ], Response::HTTP_BAD_REQUEST);
            }
            return response([
                'status' => 'Error',
                'message' => 'Something Went Wrong'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        parent::render($request, $exception);
    }
}
