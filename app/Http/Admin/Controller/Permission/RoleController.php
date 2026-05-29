<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\BatchGrantPermissionsForRoleRequest;
use App\Http\Admin\Request\RoleRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\Permission\Menu;
use App\Service\Permission\RoleService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('角色管理', '角色增删改查和角色权限分配')]
class RoleController extends AbstractController
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    #[Endpoint('roleList', '角色列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    #[Response(type: 'array{code: int, message: string, data: array{list: array<int, \App\Models\Permission\Role>, total: int}}')]
    public function pageList(Request $request): JsonResponse
    {
        return Result::success($this->roleService->page(
            $request->all(),
            (int) $request->input('page', 1),
            (int) $request->input('page_size', 10),
        ));
    }

    #[Endpoint('roleCreate', '创建角色')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(RoleRequest $request): JsonResponse
    {
        $this->roleService->create(array_merge($request->validated(), [
            'created_by' => $request->user('api')->id,
        ]));

        return Result::success();
    }

    #[Endpoint('roleSave', '保存角色')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function save(int $id, RoleRequest $request): JsonResponse
    {
        $this->roleService->updateById($id, array_merge($request->validated(), [
            'updated_by' => $request->user('api')->id,
        ]));

        return Result::success();
    }

    #[Endpoint('roleDelete', '删除角色')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->roleService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }

    #[Endpoint('setRolePermission', '获取角色权限列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function getRolePermissionForRole(int $id): JsonResponse
    {
        return Result::success($this->roleService->getRolePermission($id)->map(static fn (Menu $menu): array => $menu->only([
            'id', 'name',
        ]))->toArray());
    }

    #[Endpoint('roleGrantPermissions', '赋予角色权限')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function batchGrantPermissionsForRole(int $id, BatchGrantPermissionsForRoleRequest $request): JsonResponse
    {
        $this->roleService->batchGrantPermissionsForRole($id, Arr::get($request->validated(), 'permissions', []));

        return Result::success();
    }
}
