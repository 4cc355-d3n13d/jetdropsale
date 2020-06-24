<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(\Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Exception $e)
    {
        if ($request->wantsJson()) {
            $return = [
                'result' => 'error',
            ];
            $code = 500;
            switch (true) {
                case $e instanceof ModelNotFoundException:
                    $return['message'] = 'Record not found';
                    $code = 404;
                    break;
                case $e instanceof NotFoundHttpException:
                    $return['message'] = 'Route not found';
                    $code = 404;
                    break;
                case $e instanceof AuthenticationException:
                    $return['message'] = "Client is not authenticated";
                    $code = 401;
                    break;
                case $e instanceof AuthorizationException:
                    $return['message'] = "Client is not authorized";
                    $code = 403;
                    break;
                case $e instanceof HttpException:
                    $return['message'] = $e->getMessage();
                    $code= $e->getStatusCode();
                    break;
                default:
                    $return['message'] = $e->getMessage();
            }

            return response()->json($return, $code);
        }

        return parent::render($request, $e);
    }
}
