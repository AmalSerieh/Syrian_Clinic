<?php

use App\Http\Middleware\ForceProfileUpdate;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'forceProfileUpdate' => ForceProfileUpdate::class,
    ]);

    $middleware->api(prepend: [
        SetLocale::class,
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ]);

    $middleware->use([
        SetLocale::class,
    ]);

    $middleware->web(append: [
        SetLocale::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
