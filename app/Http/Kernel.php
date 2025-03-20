<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * Estos middleware se aplican a todas las solicitudes de la aplicación.
     *
     * @var array
     */
    protected $middleware = [
        // ...
    ];

    /**
     * Las rutas de middleware de la aplicación.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class, // Verifica que esto esté incluido
        ],
    ];

    /**
     * Los middleware de las rutas específicas.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'csrf' => \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        // otros middlewares
    ];

}
