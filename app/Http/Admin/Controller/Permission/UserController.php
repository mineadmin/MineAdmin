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
use App\Http\Admin\Request\Permission\BatchGrantRolesForUserRequest;
use App\Http\Admin\Request\Permission\UserRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Schema\UserSchema;
use App\Service\Permission\UserService;
use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Request;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Kernel\Swagger\Attributes\PageResponse;
use Mine\Kernel\Swagger\Attributes\ResultResponse;
use OpenApi\Attributes\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AuthMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
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
    #[Permission(code: 'user:list')]
    #[PageResponse(instance: UserSchema::class)]
    public function pageList(Request $request): Result
    {
        return $this->success(
            $this->userService->page(
                $request->all(),
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
    #[Permission(code: 'user:info')]
    #[ResultResponse(new Result())]
    public function updateInfo(UserRequest $request): Result
    {
        $this->userService->updateById($this->currentUser->id(), Arr::except($request->validated(), ['password']));
        return $this->success();
    }

    #[Put(
        path: '/admin/user/password',
        operationId: 'updatePassword',
        summary: '修改密码',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:password')]
    #[ResultResponse(new Result())]
    public function resetPassword(): Result
    {
        $this->userService->resetPassword($this->currentUser->id());
        return $this->success();
    }

    #[Post(
        path: '/admin/user',
        operationId: 'userCreate',
        summary: '创建用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:create')]
    #[RequestBody(content: new JsonContent(ref: UserRequest::class, title: '创建用户'))]
    #[ResultResponse(new Result())]
    public function create(UserRequest $request): Result
    {
        $this->userService->create($request->validated());
        return $this->success();
    }

    #[Delete(
        path: '/admin/user',
        operationId: 'userDelete',
        summary: '删除用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:delete')]
    #[ResultResponse(new Result())]
    public function delete(Request $request): Result
    {
        $this->userService->deleteById($request->all(), false);
        return $this->success();
    }

    #[Put(
        path: '/admin/user/{userId}',
        operationId: 'userUpdate',
        summary: '更新用户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:save')]
    #[RequestBody(content: new JsonContent(ref: UserRequest::class, title: '更新用户'))]
    #[ResultResponse(new Result())]
    public function save(int $userId, UserRequest $request): Result
    {
        $this->userService->updateById($userId, $request->validated());
        return $this->success();
    }

    #[Put(
        path: '/admin/user/{id}/role',
        operationId: 'batchGrantRolesForUser',
        summary: '批量授权用户角色',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:role')]
    #[RequestBody(content: new JsonContent(ref: BatchGrantRolesForUserRequest::class, title: '批量授权用户角色'))]
    #[ResultResponse(new Result())]
    public function batchGrantRolesForUser(int $id, BatchGrantRolesForUserRequest $request): Result
    {
        $this->userService->batchGrantRoleForUser($id, $request->input('role_codes'));
        return $this->success();
    }
}
