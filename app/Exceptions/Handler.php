<?php

namespace App\Exceptions;

use App\Traits\RespondsWithHttpStatus;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use RespondsWithHttpStatus;

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
        $this->reportable(fn (CustomException $e) => false)->stop();

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->failure('Model not found!', status: 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->failure('Invalid route!', status: 404);
        }

        if ($e instanceof ValidationException) {
            return $this->failure('Invalid payload!', $e->errors(), 422);
        }

        if ($e instanceof Exception) {
            return $this->failure($e->getMessage(), status: 403);
        }

        return parent::render($request, $e);
    }

    /**
     * @param $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return $this->failure('Unauthenticated!', status: 401);
    }
}
