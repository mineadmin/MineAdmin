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
use App\Http\Admin\Request\Permission\BatchGrantDataPermissionForPositionRequest;
use App\Http\Admin\Request\Permission\PositionRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\PositionSchema;
use App\Service\Permission\PositionService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
class PositionController extends AbstractController
{
    public function __construct(
        protected readonly CurrentUser $currentUser,
        protected readonly PositionService $service
    ) {}

    #[Get(
        path: '/admin/position/list',
        operationId: 'positionList',
        summary: '岗位列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['岗位管理'],
    )]
    #[PageResponse(instance: PositionSchema::class)]
    #[Permission(code: 'permission:position:index')]
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

    #[Put(
        path: '/admin/position/{id}/data_permission',
        operationId: 'positionDataPermission',
        summary: '设置岗位数据权限',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['岗位管理'],
    )]
    #[Permission(code: 'permission:position:data_permission')]
    #[ResultResponse(instance: new Result())]
    public function batchDataPermission(int $id, BatchGrantDataPermissionForPositionRequest $request): Result
    {
        $this->service->batchDataPermission($id, $request->validated());
        return $this->success();
    }

    #[Post(
        path: '/admin/position',
        operationId: 'positionCreate',
        summary: '创建岗位',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['岗位管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: PositionRequest::class)
    )]
    #[Permission(code: 'permission:position:save')]
    #[ResultResponse(instance: new Result())]
    public function create(PositionRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/position/{id}',
        operationId: 'positionSave',
        summary: '保存岗位',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['岗位管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: PositionRequest::class)
    )]
    #[Permission(code: 'permission:position:update')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, PositionRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/position',
        operationId: 'positionDelete',
        summary: '删除岗位',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['岗位管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'permission:position:delete')]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }
}
