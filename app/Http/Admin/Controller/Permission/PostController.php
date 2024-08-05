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
use App\Http\Admin\Request\PostRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Service\Permission\PostService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use OpenApi\Attributes\RequestBody;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AuthMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
class PostController extends AbstractController
{
    public function __construct(
        private readonly CurrentUser $user,
        private readonly PostService $service
    ) {}

    #[Get(
        path: '/admin/post/list',
        operationId: 'postList',
        summary: '岗位列表',
        security: [['bearerAuth' => []]],
        tags: ['岗位管理']
    )]
    #[Permission('post:list')]
    #[ResultResponse(instance: new Result())]
    public function pageList(): Result
    {
        return $this->success($this->service->page(
            $this->getRequest()->all(),
            $this->getCurrentPage(),
            $this->getPageSize()
        ));
    }

    #[Post(
        path: '/admin/post',
        operationId: 'postCreate',
        summary: '岗位创建',
        security: [['bearerAuth' => []]],
        tags: ['岗位管理']
    )]
    #[Permission('post:create')]
    #[RequestBody(content: new JsonContent(ref: PostRequest::class))]
    #[ResultResponse(instance: new Result())]
    public function create(PostRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->user->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/post/{id}',
        operationId: 'postSave',
        summary: '岗位保存',
        security: [['bearerAuth' => []]],
        tags: ['岗位管理']
    )]
    #[Permission('post:save')]
    #[ResultResponse(instance: new Result())]
    #[RequestBody(content: new JsonContent(ref: PostRequest::class))]
    public function save(int $id, PostRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->user->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/post/{id}',
        operationId: 'postDelete',
        summary: '岗位删除',
        security: [['bearerAuth' => []]],
        tags: ['岗位管理']
    )]
    #[Permission('post:delete')]
    #[ResultResponse(instance: new Result())]
    public function delete(int $id): Result
    {
        $this->service->deleteById($id, false);
        return $this->success();
    }
}
