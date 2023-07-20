<?php

namespace App\Exceptions;

use App\Traits\RespondsWithHttpStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use UnhandledMatchError;

class Handler extends ExceptionHandler
{
    use RespondsWithHttpStatus;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (CustomException $e) {
        })->stop();

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            if ($e instanceof NotFoundHttpException)
                return $this->failure('Invalid route!', status: 404);

            if ($e instanceof ModelNotFoundException)
                return $this->failure('Model not found!', status: 404);

            if ($e instanceof MethodNotAllowedHttpException)
                return $this->failure(details: $e->getMessage(), status: 405);

            if ($e instanceof ThrottleRequestsException)
                return $this->failure('Too many requests!', status: 429);

            if ($e instanceof AuthorizationException)
                return $this->failure($e->getMessage() ?? 'You don\'t own this resource!', status: 403);

            if ($e instanceof PostTooLargeException)
                return $this->failure('Payload too large!', status: 413);

            if ($e instanceof HttpException)
                return $this->failure('You don\'t have the right permissions to access this resource', status: 403);

            if ($e instanceof ValidationException)
                return $this->failure('Invalid payload!', $e->errors(), 422);
                
            if($e instanceof RouteNotFoundException)
                return $this->failure('Route not found', 400);

            if($e instanceof UnhandledMatchError){
                logger($e->getMessage());
                return $this->failure(message:'Invalid type ', status:422);
            }

            if ($e instanceof CustomException)
                return $this->failure($e->getMessage() ?? 'An error occurred!', status: 400);
        }
        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse|Response
    {
        return $this->failure('Unauthenticated.', status: 401);
    }
}
