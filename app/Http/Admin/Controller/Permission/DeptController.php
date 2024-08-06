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
use App\Http\Admin\CurrentUser;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\DeptRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Service\Permission\DeptService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AuthMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
final class DeptController extends AbstractController
{
    public function __construct(
        private readonly CurrentUser $user,
        private readonly DeptService $service
    ) {}

    #[Get(
        path: '/admin/dept/list',
        operationId: 'deptList',
        summary: '部门列表',
        security: [['bearerAuth' => []]],
        tags: ['部门管理']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dept:list')]
    public function pageList(): Result
    {
        return $this->success($this->service->page(
            $this->getRequest()->all(),
            $this->getPageSize(),
            $this->getPageSize()
        ));
    }

    #[Post(
        path: '/admin/dept',
        operationId: 'deptCreate',
        summary: '部门创建',
        security: [['bearerAuth' => []]],
        tags: ['部门管理']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dept:create')]
    #[RequestBody(content: new JsonContent(ref: DeptRequest::class))]
    public function create(DeptRequest $request): Result
    {
        $this->service->create(array_merge(
            $request->validated(),
            ['created_by' => $this->user->id()]
        ));
        return $this->success();
    }

    #[Put(
        path: '/admin/dept/{id}',
        operationId: 'deptSave',
        summary: '部门保存',
        security: [['bearerAuth' => []]],
        tags: ['部门管理']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dept:save')]
    #[RequestBody(content: new JsonContent(ref: DeptRequest::class))]
    public function save(int $id, DeptRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->user->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/dept/{id}',
        operationId: 'deptDelete',
        summary: '部门删除',
        security: [['bearerAuth' => []]],
        tags: ['部门管理']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dept:delete')]
    public function delete(int $id): Result
    {
        $this->service->deleteById($id, false);
        return $this->success();
    }
}
