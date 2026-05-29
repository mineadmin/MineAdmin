<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\BatchGrantRolesForUserRequest;
use App\Http\Admin\Request\DeleteUsersRequest;
use App\Http\Admin\Request\UserRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\Permission\Role;
use App\Models\User;
use App\Service\Permission\UserService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('用户管理', '用户增删改查和用户角色分配')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Endpoint('userList', '用户列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    #[Response(type: 'array{code: int, message: string, data: array{list: array<int, \App\Models\User>, total: int}}')]
    public function pageList(Request $request): JsonResponse
    {
        return Result::success($this->userService->page(
            $request->all(),
            (int) $request->input('page', 1),
            (int) $request->input('page_size', 10),
            $this->user($request),
        ));
    }

    #[Endpoint('updateInfo', '更新用户信息')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function updateInfo(UserRequest $request): JsonResponse
    {
        $this->userService->updateById($this->user($request)->id, Arr::except($request->validated(), ['password']));

        return Result::success();
    }

    #[Endpoint('updatePassword', '重置密码')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->userService->resetPassword($request->integer('id') ?: null)
            ? Result::success()
            : Result::fail();
    }

    #[Endpoint('userCreate', '创建用户')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(UserRequest $request): JsonResponse
    {
        $this->userService->create(array_merge($request->validated(), [
            'created_by' => $this->user($request)->id,
        ]));

        return Result::success();
    }

    #[Endpoint('userDelete', '删除用户')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(DeleteUsersRequest $request): JsonResponse
    {
        $this->userService->deleteById(Arr::wrap($request->validated()));

        return Result::success();
    }

    #[Endpoint('userUpdate', '更新用户')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function save(int $userId, UserRequest $request): JsonResponse
    {
        $this->userService->updateById($userId, array_merge($request->validated(), [
            'updated_by' => $this->user($request)->id,
        ]));

        return Result::success();
    }

    #[Endpoint('getUserRole', '获取用户角色列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function getUserRole(int $userId): JsonResponse
    {
        return Result::success($this->userService->getUserRole($userId)->map(static fn (Role $role): array => $role->only([
            'id',
            'code',
            'name',
        ]))->toArray());
    }

    #[Endpoint('batchGrantRolesForUser', '批量授权用户角色')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function batchGrantRolesForUser(int $userId, BatchGrantRolesForUserRequest $request): JsonResponse
    {
        $this->userService->batchGrantRoleForUser($userId, Arr::get($request->validated(), 'role_codes', []));

        return Result::success();
    }

    private function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user('api');

        return $user;
    }
}
