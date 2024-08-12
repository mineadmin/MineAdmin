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
use App\Http\Admin\Request\Setting\ConfigGroupRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\ListResponse;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Schema\ConfigGroupSchema;
use App\Service\Setting\ConfigGroupService;
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
class ConfigGroupController extends AbstractController
{
    public function __construct(
        protected readonly ConfigGroupService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/config/group',
        operationId: 'configGroupList',
        summary: '配置分组列表',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:group:list')]
    #[ListResponse(
        instance: ConfigGroupSchema::class
    )]
    public function list(): Result
    {
        return $this->success(
            $this->service->getList(
                $this->getRequest()->all()
            )
        );
    }

    #[Post(
        path: '/admin/config/group',
        operationId: 'configGroupCreate',
        summary: '添加配置分组',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:group:create')]
    #[RequestBody(
        content: new JsonContent(ref: ConfigGroupSchema::class)
    )]
    #[ResultResponse(instance: new Result())]
    public function create(ConfigGroupRequest $request): Result
    {
        $data = $request->validated();
        $data['created_by'] = $this->currentUser->id();
        $this->service->create($data);
        return $this->success();
    }

    #[Put(
        path: '/admin/config/group/{id}',
        operationId: 'configGroupEdit',
        summary: '编辑配置分组',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:group:edit')]
    #[RequestBody(
        content: new JsonContent(ref: ConfigGroupSchema::class)
    )]
    #[ResultResponse(instance: new Result())]
    public function edit(int $id, ConfigGroupRequest $request): Result
    {
        if (! $this->service->existsById($id)) {
            return $this->error(trans('config_group.not_found'));
        }
        $data = $request->validated();
        $data['updated_by'] = $this->currentUser->id();
        $this->service->updateById($id, $data);
        return $this->success();
    }

    #[Delete(
        path: '/admin/config/group/{id}',
        operationId: 'configGroupDelete',
        summary: '删除配置分组',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:group:delete')]
    #[ResultResponse(instance: new Result())]
    public function delete(int $id): Result
    {
        if (! $this->service->existsById($id)) {
            return $this->error(trans('config_group.not_found'));
        }
        $this->service->deleteById($id);
        return $this->success();
    }
}
