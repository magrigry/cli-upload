<?php

use App\Exceptions\InsufficientStorage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '/api',
    )
    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {

            $accepted = collect($request->getAcceptableContentTypes());

            if ($accepted->first() === 'text/plain') {

                $message = 'Internal Server Error';

                if ($e instanceof NotFoundHttpException) {
                    $message = '404 Not Found';
                }

                if ($e instanceof InsufficientStorage) {
                    $message = 'The server has reached is maximum capacity';
                }

                return response(
                    $message,
                    $e instanceof HttpException ? $e->getStatusCode() : 500,
                    [
                        'Content-Type' => 'text/plain',
                    ]
                );
            }

            return null;
        });
    })->create();
