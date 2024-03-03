<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * APIの場合にエラーをJSONで返す
     */
    public function render($request, $exception)
    {
        // HTTPエラー
        if ($request->is('api/*')) {
            if ($this->isHttpException($exception)) {
                return response()->json([
                    'result' => 'fail',
                    'message' => $exception->getMessage()
                ], $exception->getCode());
            }
            // laravel的エラーは400(Bad Request)を返す
            return response()->json([
                'result' => 'fail',
                'message' => $exception->getMessage()
            ], 400);
        }
        return parent::render($request, $exception);
    }
}
