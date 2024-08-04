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
use App\Http\Admin\Request\Permission\Role\CreateRequest;
use App\Http\Admin\Request\Permission\Role\SaveRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\PageResponse;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Schema\RoleSchema;
use App\Service\Permission\RoleService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(PermissionMiddleware::class)]
class RoleController extends AbstractController
{
    public function __construct(
        private readonly RoleService $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/role/list',
        operationId: 'roleList',
        summary: '角色列表',
        security: [['bearerAuth' => []]],
        tags: ['角色管理'],
    )]
    #[PageResponse(instance: RoleSchema::class)]
    #[Permission(code: 'role:list')]
    public function pageList(RequestInterface $request): Result
    {
        return $this->success(
            $this->service->page(
                $request->all(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Post(
        path: '/admin/role',
        operationId: 'roleCreate',
        summary: '创建角色',
        security: [['bearerAuth' => []]],
        tags: ['角色管理'],
    )]
    #[RequestBody(
        request: CreateRequest::class,
        description: '创建角色',
        required: true
    )]
    #[Permission(code: 'role:create')]
    #[ResultResponse(instance: new Result())]
    public function create(CreateRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/role/{id}',
        operationId: 'roleSave',
        summary: '保存角色',
        security: [['bearerAuth' => []]],
        tags: ['角色管理'],
    )]
    #[RequestBody(
        request: SaveRequest::class,
        description: '保存角色',
        required: true
    )]
    #[Permission(code: 'role:save')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, SaveRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/role/{id}',
        operationId: 'roleDelete',
        summary: '删除角色',
        security: [['bearerAuth' => []]],
        tags: ['角色管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'role:delete')]
    public function delete(int $id): Result
    {
        $this->service->deleteById($id, false);
        return $this->success();
    }
}
