<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        // Agrega esta línea para permitir peticiones desde Postman a estas rutas
        $middleware->validateCsrfTokens(except: [
            'rutinas/*', // Esto desbloquea importar, crear, editar y borrar rutinas
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
