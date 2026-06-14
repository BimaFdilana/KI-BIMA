<?php

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
            'kasir' => \App\Http\Middleware\EnsureKasirRole::class,
            'https' => \App\Http\Middleware\ForceHttps::class,
        ]);

        $middleware->trustProxies(at: '*');
        // API Middleware Stack
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Global Middleware
        $middleware->append([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Middleware Aliases
        $middleware->alias([
            // Spatie Permission Middleware
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // Custom Verification Middleware
            'verified.phone' => \App\Http\Middleware\EnsurePhoneIsVerified::class,
            'verified.device' => \App\Http\Middleware\EnsureDeviceIsVerified::class,
            'verified.device.api' => \App\Http\Middleware\EnsureDeviceIsVerifiedApi::class,
            // Custom Auth Middleware
            'guest' => \App\Http\Middleware\RedirectIfAuth::class,
            'auth.check' => \App\Http\Middleware\RedirectIfNoAuth::class,
            // Toko Access Middleware
            'toko' => \App\Http\Middleware\TokoAccessMiddleware::class,
        ]);

        // Middleware Groups - Use standard 'api' rate limiter
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/midtrans/notification',
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API Exception Handling
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error',
                    'error' => app()->hasDebugModeEnabled() ? $e->getMessage() : 'Internal Server Error'
                ], 500);
            }
        });
    })->create();