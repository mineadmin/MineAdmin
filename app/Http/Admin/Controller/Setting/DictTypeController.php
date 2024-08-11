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

namespace App\Http\Admin\Controller\Setting;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\CurrentUser;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\DictTypeRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Service\Setting\DictTypeService;
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
final class DictTypeController extends AbstractController
{
    public function __construct(
        private readonly CurrentUser $user,
        private readonly DictTypeService $service
    ) {}

    #[Get(
        path: '/admin/dictType/list',
        operationId: 'dictTypeList',
        summary: '字典类型列表',
        security: [['bearerAuth' => []]],
        tags: ['字典类型']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dictType:list')]
    public function pageList(): Result
    {
        return $this->success($this->service->page(
            $this->getRequest()->all(),
            $this->getCurrentPage(),
            $this->getPageSize()
        ));
    }

    #[Post(
        path: '/admin/dictType',
        operationId: 'dictTypeCreate',
        summary: '字典类型创建',
        security: [['bearerAuth' => []]],
        tags: ['字典类型']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dictType:create')]
    #[RequestBody(content: new JsonContent(ref: DictTypeRequest::class))]
    public function create(DictTypeRequest $request): Result
    {
        $this->service->create(array_merge(
            $request->validated(),
            ['created_by' => $this->user->id()]
        ));
        return $this->success();
    }

    #[Put(
        path: '/admin/dictType/{id}',
        operationId: 'deptSave',
        summary: '字典类型保存',
        security: [['bearerAuth' => []]],
        tags: ['字典类型']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dictType:save')]
    #[RequestBody(content: new JsonContent(ref: DictTypeRequest::class))]
    public function save(int $id, DictTypeRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->user->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/dictType/{id}',
        operationId: 'dictTypeDelete',
        summary: '字典类型删除',
        security: [['bearerAuth' => []]],
        tags: ['字典类型']
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission('dictType:delete')]
    public function delete(int $id): Result
    {
        $this->service->deleteDictTypeAndData($id, false);
        return $this->success();
    }
}
