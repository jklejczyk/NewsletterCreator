<?php

use App\Domain\Newsletter\Exceptions\ConfirmationTokenExpiredException;
use App\Domain\Newsletter\Exceptions\ConfirmationTokenInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*'));

        $exceptions->render(fn (ModelNotFoundException $e) => response()->json(
            ['message' => 'Nie znaleziono zasobu.'],
            404,
        ));

        $exceptions->render(fn (ConfirmationTokenExpiredException $e) => response()->json(
            ['message' => 'Link potwierdzający wygasł. Zarejestruj się ponownie.'],
            410,
        ));

        $exceptions->render(fn (ConfirmationTokenInvalidException $e) => response()->json(
            ['message' => 'Nieprawidłowy link potwierdzający.'],
            404,
        ));
    })->create();
