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

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\Permission\LeaderRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\LeaderSchema;
use App\Service\Permission\LeaderService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\RequestBody;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
class LeaderController extends AbstractController
{
    public function __construct(
        protected readonly CurrentUser $currentUser,
        protected readonly LeaderService $service
    ) {}

    #[Get(
        path: '/admin/leader/list',
        operationId: 'leaderList',
        summary: '部门领导列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['部门领导管理'],
    )]
    #[PageResponse(instance: LeaderSchema::class)]
    #[Permission(code: 'permission:leader:index')]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Post(
        path: '/admin/leader',
        operationId: 'leaderCreate',
        summary: '部门领导岗位',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['部门领导管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: LeaderRequest::class)
    )]
    #[Permission(code: 'permission:leader:save')]
    #[ResultResponse(instance: new Result())]
    public function create(LeaderRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/leader',
        operationId: 'leaderDelete',
        summary: '删除部门领导',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['部门领导管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'permission:leader:delete')]
    public function delete(): Result
    {
        $this->service->deleteByDoubleKey($this->getRequestData());
        return $this->success();
    }
}
