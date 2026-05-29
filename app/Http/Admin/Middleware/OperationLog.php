<?php

namespace App\Http\Admin\Middleware;

use App\Http\Common\ResultCode;
use App\Models\User;
use App\Service\Permission\UserOperationLogService;
use Closure;
use Dedoc\Scramble\Attributes\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\defer;

class OperationLog
{
    public function __construct(private readonly UserOperationLogService $operationLogService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->isSuccessfulResult($response)) {
            $this->record($request);
        }

        return $response;
    }

    private function isSuccessfulResult(Response $response): bool
    {
        if (! $response->isSuccessful()) {
            return false;
        }

        $data = json_decode($response->getContent(), true);

        return is_array($data) && ($data['code'] ?? null) === ResultCode::Success->value;
    }

    private function record(Request $request): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        /** @var User|null $user */
        $user = $request->user('api');
        if ($user === null) {
            return;
        }

        $payload = [
            'username' => $user->username,
            'method' => $request->method(),
            'router' => '/'.$request->path(),
            'service_name' => $this->operationName($request),
            'ip' => $request->ip(),
        ];

        defer(fn () => $this->operationLogService->record($payload))->always();
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is('admin/user-operation-log*');
    }

    private function operationName(Request $request): string
    {
        $controller = $request->route()?->getAction('controller');
        if (! is_string($controller) || ! str_contains($controller, '@')) {
            return Str::limit($request->route()?->getName() ?? $request->path(), 30, '');
        }

        [$class, $method] = explode('@', $controller, 2);
        if (! method_exists($class, $method)) {
            return Str::limit(class_basename($class).'@'.$method, 30, '');
        }

        $attributes = (new ReflectionMethod($class, $method))->getAttributes(Endpoint::class);
        if ($attributes === []) {
            return Str::limit(class_basename($class).'@'.$method, 30, '');
        }

        $endpoint = $attributes[0]->newInstance();

        return Str::limit($endpoint->title ?? $endpoint->operationId ?? class_basename($class).'@'.$method, 30, '');
    }
}
