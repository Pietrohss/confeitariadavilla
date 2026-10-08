<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',        
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API não redireciona: sem token responde 401 JSON (ver withExceptions)
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('api/*') ? null : route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Erros da API (validação, 404...) sempre em JSON, mesmo sem header Accept
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado.'
                ], 401);
            }
        });
    })->create();
