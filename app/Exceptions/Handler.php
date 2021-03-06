<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Notification for 403 error Unauthorized user call action
     * @param  [type]    $request   [description]
     * @param  Throwable $exception [description]
     * @return [type]               [description]
     */
    private function unauthorized($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
        toastr()->warning($exception->getMessage(), 'Warning');
        return redirect()->back();
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 401);
            }
        }
        // Check Authorization for callAction in contoller
        if ($exception instanceof AuthorizationException) {
            return $this->unauthorized($request, $exception);
        }
        return parent::render($request, $exception);
    }
}
