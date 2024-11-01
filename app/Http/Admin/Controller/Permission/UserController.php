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
use App\Http\Admin\Request\Permission\BatchGrantRolesForUserRequest;
use App\Http\Admin\Request\Permission\UserRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Model\Permission\Role;
use App\Schema\UserSchema;
use App\Service\Permission\UserService;
use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;
use OpenApi\Attributes\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/user/list',
        operationId: 'userList',
        summary: '用户列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:index')]
    #[PageResponse(instance: UserSchema::class)]
    public function pageList(): Result
    {
        return $this->success(
            $this->userService->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Put(
        path: '/admin/user',
        operationId: 'updateInfo',
        summary: '更新用户信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )
    ]
    #[RequestBody(content: new JsonContent(ref: UserRequest::class, title: '修改个人信息'))]
    #[Permission(code: 'permission:user:update')]
    #[ResultResponse(new Result())]
    public function updateInfo(UserRequest $request): Result
    {
        $this->userService->updateById($this->currentUser->id(), Arr::except($request->validated(), ['password']));
        return $this->success();
    }

    #[Put(
        path: '/admin/user/password',
        operationId: 'updatePassword',
        summary: '重置密码',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:password')]
    #[ResultResponse(new Result())]
    public function resetPassword(): Result
    {
        return $this->userService->resetPassword($this->getRequest()->input('id'))
            ? $this->success()
            : $this->error();
    }

    #[Post(
        path: '/admin/user',
        operationId: 'userCreate',
        summary: '创建用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:save')]
    #[RequestBody(content: new JsonContent(ref: UserRequest::class, title: '创建用户'))]
    #[ResultResponse(new Result())]
    public function create(UserRequest $request): Result
    {
        $this->userService->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/user',
        operationId: 'userDelete',
        summary: '删除用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:delete')]
    #[ResultResponse(new Result())]
    public function delete(): Result
    {
        $this->userService->deleteById($this->getRequestData());
        return $this->success();
    }

    #[Put(
        path: '/admin/user/{userId}',
        operationId: 'userUpdate',
        summary: '更新用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:update')]
    #[RequestBody(content: new JsonContent(ref: UserRequest::class, title: '更新用户'))]
    #[ResultResponse(new Result())]
    public function save(int $userId, UserRequest $request): Result
    {
        $this->userService->updateById($userId, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Get(
        path: '/admin/user/{userId}/roles',
        operationId: 'getUserRole',
        summary: '获取用户角色列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:getRole')]
    #[ResultResponse(new Result())]
    public function getUserRole(int $userId): Result
    {
        return $this->success($this->userService->getUserRole($userId)->map(static fn (Role $role) => $role->only([
            'id',
            'code',
            'name',
        ])));
    }

    #[Put(
        path: '/admin/user/{userId}/roles',
        operationId: 'batchGrantRolesForUser',
        summary: '批量授权用户角色',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'permission:user:setRole')]
    #[RequestBody(content: new JsonContent(ref: BatchGrantRolesForUserRequest::class, title: '批量授权用户角色'))]
    #[ResultResponse(new Result())]
    public function batchGrantRolesForUser(int $userId, BatchGrantRolesForUserRequest $request): Result
    {
        $this->userService->batchGrantRoleForUser($userId, $request->input('role_codes'));
        return $this->success();
    }
}
