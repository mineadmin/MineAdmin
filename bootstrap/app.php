<?php

use App\Exception\BusinessException;
use App\Http\Admin\Middleware\EnsureAccessToken;
use App\Http\Admin\Middleware\EnsurePermission;
use App\Http\Admin\Middleware\OperationLog;
use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'access.token' => EnsureAccessToken::class,
            'operation.log' => OperationLog::class,
            'permission' => EnsurePermission::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            return $request->is('admin/*') || $request->is('api/*') ? null : route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $exception): bool {
            return $request->is('admin/*') || $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (BusinessException $exception, Request $request): JsonResponse {
            return Result::json($exception->resultCode, $exception->data ?? [], $exception->getMessage());
        });

        $exceptions->render(function (ValidationException $exception, Request $request): JsonResponse {
            return Result::json(ResultCode::Unprocessable, $exception->errors(), collect($exception->errors())->flatten()->first() ?? $exception->getMessage());
        });

        $exceptions->render(function (AuthenticationException|JWTException|UnauthorizedHttpException $exception, Request $request): JsonResponse {
            return Result::json(ResultCode::Unauthorized, [], trans('auth.unauthenticated'));
        });
    })->create();
