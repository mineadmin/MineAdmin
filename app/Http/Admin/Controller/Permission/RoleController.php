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

use App\Exception\BusinessException;
use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\Permission\BatchGrantPermissionsForRoleRequest;
use App\Http\Admin\Request\Permission\RoleRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use App\Http\CurrentUser;
use App\Model\Permission\Menu;
use App\Schema\RoleSchema;
use App\Service\Permission\RoleService;
use Hyperf\Collection\Arr;
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
final class RoleController extends AbstractController
{
    public function __construct(
        private readonly RoleService $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/role/list',
        operationId: 'roleList',
        summary: '角色列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[PageResponse(instance: RoleSchema::class)]
    #[Permission(code: 'permission:role:index')]
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
        path: '/admin/role',
        operationId: 'roleCreate',
        summary: '创建角色',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: RoleRequest::class)
    )]
    #[Permission(code: 'permission:role:save')]
    #[ResultResponse(instance: new Result())]
    public function create(RoleRequest $request): Result
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
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: RoleRequest::class)
    )]
    #[Permission(code: 'permission:role:update')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, RoleRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/role',
        operationId: 'roleDelete',
        summary: '删除角色',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'permission:role:delete')]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }

    #[Get(
        path: '/admin/role/{id}/permissions',
        operationId: 'setRolePermission',
        summary: '获取角色权限列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[ResultResponse(
        instance: new Result(),
        example: '{"code":200,"message":"成功","data":[{"id":59,"name":"xdrljpefIZ"},{"id":60,"name":"GIdOejHL2R"},{"id":61,"name":"ZpEnJv00VG"}]}'
    )]
    #[Permission(code: 'permission:role:getMenu')]
    public function getRolePermissionForRole(int $id): Result
    {
        return $this->success($this->service->getRolePermission($id)->map(static fn (Menu $menu) => $menu->only([
            'id', 'name',
        ]))->toArray());
    }

    #[Put(
        path: '/admin/role/{id}/permissions',
        operationId: 'roleGrantPermissions',
        summary: '赋予角色权限',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['角色管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[RequestBody(content: new JsonContent(
        ref: BatchGrantPermissionsForRoleRequest::class
    ))]
    #[Permission(code: 'permission:role:setMenu')]
    public function batchGrantPermissionsForRole(int $id, BatchGrantPermissionsForRoleRequest $request): Result
    {
        if (! $this->service->existsById($id)) {
            throw new BusinessException(code: ResultCode::NOT_FOUND);
        }
        $permissionsCode = Arr::get($request->validated(), 'permissions', []);
        $this->service->batchGrantPermissionsForRole($id, $permissionsCode);
        return $this->success();
    }
}
