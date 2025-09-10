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

namespace App\Http\Admin\Controller\Logstash;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\UserLoginLogSchema;
use App\Service\Logstash\UserLoginLogService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
final class UserLoginLogController extends AbstractController
{
    public function __construct(
        protected readonly UserLoginLogService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/user-login-log/list',
        operationId: 'UserLoginLogList',
        summary: '用户登录日志列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['系统管理'],
    )]
    #[Permission(code: 'log:userLogin:list')]
    #[PageResponse(instance: UserLoginLogSchema::class)]
    public function page(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Delete(
        path: '/admin/user-login-log',
        operationId: 'UserLoginLogDelete',
        summary: '删除用户登录日志',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['系统管理'],
    )]
    #[Permission(code: 'log:userLogin:delete')]
    public function delete(RequestInterface $request): Result
    {
        $this->service->deleteById($request->input('ids'));
        return $this->success();
    }
}
