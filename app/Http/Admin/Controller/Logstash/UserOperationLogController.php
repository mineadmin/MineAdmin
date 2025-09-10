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
use App\Schema\UserOperationLogSchema;
use App\Service\Logstash\UserOperationLogService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
final class UserOperationLogController extends AbstractController
{
    public function __construct(
        protected readonly UserOperationLogService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/user-operation-log/list',
        operationId: 'UserOperationLogList',
        summary: '用户操作日志列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['系统管理'],
    )]
    #[Permission(code: 'log:userOperation:list')]
    #[PageResponse(instance: UserOperationLogSchema::class)]
    public function page(): Result
    {
        return $this->success($this->service->page(
            $this->getRequestData(),
            $this->getCurrentPage(),
            $this->getPageSize()
        ));
    }

    #[Delete(
        path: '/admin/user-operation-log',
        operationId: 'UserOperationLogDelete',
        summary: '删除用户操作日志',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['系统管理'],
    )]
    #[Permission(code: 'log:userOperation:delete')]
    #[ResultResponse(instance: Result::class)]
    public function delete(RequestInterface $request): Result
    {
        $this->service->deleteById($request->input('ids'));
        return $this->success();
    }
}
