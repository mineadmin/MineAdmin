<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Admin\Middleware;

use App\Exception\BusinessException;
use App\Http\Admin\CurrentUser;
use App\Http\Common\ResultCode;
use App\Kernel\Annotation\Permission;
use App\Kernel\Traits\ParserRouterTrait;
use App\Service\PermissionService;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PermissionMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;

    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly PermissionService $permissionService
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->check($request->getAttribute(Dispatched::class));
        return $handler->handle($request);
    }

    private function check(Dispatched $dispatched): bool
    {
        $parseResult = $this->parse($dispatched->handler->callback);
        if (! $parseResult) {
            return true;
        }
        [$controller,$method] = $parseResult;
        $annotations = AnnotationCollector::getClassMethodAnnotation($controller, $method);
        $classAnnotation = AnnotationCollector::getClassAnnotation($controller, Permission::class);
        /**
         * @var Permission[] $permissions
         */
        $permissions = [];
        $classAnnotation && $permissions[] = $classAnnotation;
        $permissions[] = Arr::get($annotations, Permission::class);
        foreach ($permissions as $permission) {
            $this->handlePermission($permission);
        }
        return true;
    }

    private function handlePermission(Permission $permission)
    {
        $operation = $permission->getOperation();
        $codes = $permission->getCode();
        $username = $this->currentUser->user()->username;
        $enforce = $this->permissionService->getEnforce();
        foreach ($codes as $code) {
            if ($operation === Permission::OPERATION_AND && ! $enforce->hasPermissionForUser($username, $code)) {
                throw new BusinessException(code: ResultCode::FORBIDDEN);
            }
            if ($operation === Permission::OPERATION_OR && $enforce->hasPermissionForUser($username, $code)) {
                return;
            }
        }
        if ($operation === Permission::OPERATION_OR) {
            throw new BusinessException(code: ResultCode::FORBIDDEN);
        }
    }
}
