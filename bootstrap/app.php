<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // 👈 ADICIONA ISSO AQUI
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cargo' => \App\Http\Middleware\CheckCargo::class,
            'permissao' => \App\Http\Middleware\CheckPermissao::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | 401 - Não autenticado
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->view('errors.401', [], 401);
        });

        /*
        |--------------------------------------------------------------------------
        | 419 - Sessão expirada / CSRF
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return response()->view('errors.419', [], 419);
        });

        /*
        |--------------------------------------------------------------------------
        | 403 - Acesso negado
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response()->view('errors.403', [], 403);
        });

        /*
        |--------------------------------------------------------------------------
        | 404 - Página não encontrada
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->view('errors.404', [], 404);
        });

        /*
        |--------------------------------------------------------------------------
        | Demais erros HTTP
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            $status = $e->getStatusCode();

            if (view()->exists("errors.$status")) {
                return response()->view("errors.$status", [], $status);
            }

            return response()->view('errors.minimal', [], $status);
        });

        /*
        |--------------------------------------------------------------------------
        | Qualquer erro interno não tratado
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (Throwable $e, Request $request) {
            return response()->view('errors.500', [], 500);
        });
    })
    ->create();