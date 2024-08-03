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
use App\Http\Admin\Request\Permission\UserCreateRequest;
use App\Http\Admin\Request\Permission\UserSaveRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\PageResponse;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Schema\UserSchema;
use App\Service\Permission\UserService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Request;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use OpenApi\Attributes\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(PermissionMiddleware::class)]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService) {}

    #[Get(
        path: '/admin/user/list',
        operationId: 'userList',
        summary: '用户列表',
        security: [['bearerAuth' => []]],
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

    #[Post(
        path: '/admin/user',
        operationId: 'userCreate',
        summary: '创建用户',
        security: [['bearerAuth' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:create')]
    #[RequestBody(content: new JsonContent(ref: UserCreateRequest::class, title: '创建用户'))]
    #[ResultResponse(new Result())]
    public function create(UserCreateRequest $request): Result
    {
        $this->userService->create($request->validated());
        return $this->success();
    }

    #[Delete(
        path: '/admin/user/{userId}',
        operationId: 'userDelete',
        summary: '删除用户',
        security: [['bearerAuth' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:delete')]
    #[ResultResponse(new Result())]
    public function delete(int $userId): Result
    {
        $this->userService->deleteById($userId, false);
        return $this->success();
    }

    #[Put(
        path: '/admin/user/{userId}',
        operationId: 'userUpdate',
        summary: '更新用户',
        security: [['bearerAuth' => []]],
        tags: ['用户管理']
    )]
    #[Permission(code: 'user:save')]
    #[RequestBody(content: new JsonContent(ref: UserSaveRequest::class, title: '更新用户'))]
    #[ResultResponse(new Result())]
    public function save(int $userId, UserSaveRequest $request): Result
    {
        $this->userService->updateById($userId, $request->validated());
        return $this->success();
    }
}
