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
use App\Http\Common\ResultCode;
use App\Http\CurrentUser;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Mine\Access\Attribute\Permission;
use Mine\Support\Traits\ParserRouterTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PermissionMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;

    public function __construct(
        private readonly CurrentUser $currentUser,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->currentUser->user();
        if ($user->status->isDisable()) {
            throw new BusinessException(code: ResultCode::DISABLED, message: trans('user.disable'));
        }
        if ($user->isSuperAdmin()) {
            return $handler->handle($request);
        }
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
        $methodPermission = Arr::get($annotations, Permission::class);
        $methodPermission && $permissions[] = $methodPermission;
        foreach ($permissions as $permission) {
            $this->handlePermission($permission);
        }
        return true;
    }

    private function handlePermission(Permission $permission): void
    {
        $operation = $permission->getOperation();
        $codes = $permission->getCode();
        foreach ($codes as $code) {
            $isMenu = $this->currentUser->user()->hasPermission($code);
            if ($operation === Permission::OPERATION_AND && ! $isMenu) {
                throw new BusinessException(code: ResultCode::FORBIDDEN);
            }
            if ($operation === Permission::OPERATION_OR && $isMenu) {
                return;
            }
        }
        if ($operation === Permission::OPERATION_OR) {
            throw new BusinessException(code: ResultCode::FORBIDDEN);
        }
    }
}
