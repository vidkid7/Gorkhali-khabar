<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\EnsureActiveSession;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\RequireRoleWeb;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/api/health',
        then: function () {
            // Preserve original port when behind nginx so URL::route() keeps ":8080"
            $appUrl = (string) env('APP_URL', '');
            if ($appUrl !== '' && str_contains($appUrl, ':')) {
                URL::forceRootUrl($appUrl);
            }

            // Admin panel mounted at a non-obvious URL (configurable). The legacy
            // `/admin` prefix is intentionally avoided so the route can't be guessed.
            $adminPath = trim((string) env('ADMIN_PATH', 'gorkhali-admin'), '/');
            if ($adminPath === '') {
                $adminPath = 'gorkhali-admin';
            }

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix($adminPath)
                ->name('admin.')
                ->group(__DIR__.'/../routes/admin.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust the nginx reverse proxy so URL generation keeps the original port
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->statefulApi();

        // Guest redirect target is the admin login route (resolved lazily so
        // the custom ADMIN_PATH prefix is honoured).
        $adminPath = trim((string) env('ADMIN_PATH', 'gorkhali-admin'), '/');
        $middleware->redirectGuestsTo(function (Request $request) use ($adminPath) {
            if ($request->is($adminPath.'/*')) {
                return route('admin.login');
            }
            return route('admin.login');
        });

        $middleware->alias([
            'active.session' => EnsureActiveSession::class,
            'role' => RequireRole::class,
            'role.web' => RequireRoleWeb::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(static fn (Request $request): bool => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(static function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error('Validation failed', 400, $exception->errors());
            }
        });

        $exceptions->render(static function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error('Authentication required', 401);
            }
        });

        $exceptions->render(static function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error('Forbidden', 403);
            }
        });

        $exceptions->render(static function (ModelNotFoundException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error('Not found', 404);
            }
        });

        $exceptions->render(static function (TooManyRequestsHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error('Too many requests', 429);
            }
        });

        $exceptions->render(static function (ApiException $exception, Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiResponse::error($exception->getMessage(), $exception->status, $exception->errors);
            }
        });

        $exceptions->render(static function (Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
                $message = $status >= 500 ? 'Internal server error' : ($exception->getMessage() ?: 'Request failed');

                return \App\Support\ApiResponse::error($message, $status);
            }
        });
    })->create();
