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
use App\Http\Admin\Request\Setting\ConfigRequest;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Annotation\Permission;
use App\Kernel\Swagger\Attributes\ListResponse;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Schema\ConfigSchema;
use App\Service\Setting\ConfigGroupService;
use App\Service\Setting\ConfigService;
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
class ConfigController extends AbstractController
{
    public function __construct(
        protected ConfigService $service,
        protected ConfigGroupService $configGroupService,
        protected CurrentUser $user
    ) {}

    #[Get(
        path: '/admin/config',
        operationId: 'configList',
        summary: '配置列表',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:list')]
    #[ListResponse(
        instance: ConfigSchema::class
    )]
    public function list(): Result
    {
        $groupId = $this->getRequest()->input('group_id', 0);
        if (! $this->configGroupService->existsById($groupId)) {
            return $this->error(trans('config_group.not_found'));
        }
        return $this->success(
            $this->service->getList(
                $this->getRequest()->all()
            )
        );
    }

    #[Post(
        path: '/admin/config',
        operationId: 'configCreate',
        summary: '创建配置',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:create')]
    #[RequestBody(content: new JsonContent(ref: ConfigSchema::class))]
    #[ResultResponse(instance: new Result())]
    public function create(ConfigRequest $request): Result
    {
        $data = $request->validated();
        $data['created_by'] = $this->user->id();
        $this->service->create($data);
        return $this->success();
    }

    #[Put(
        path: '/admin/config/{id}',
        operationId: 'configEdit',
        summary: '编辑配置',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:edit')]
    #[RequestBody(content: new JsonContent(ref: ConfigSchema::class))]
    #[ResultResponse(instance: new Result())]
    public function edit(string $id, ConfigRequest $request): Result
    {
        if (! $this->service->existsById($id)) {
            return $this->error(trans('config.not_found'));
        }
        $data = $request->validated();
        $data['updated_by'] = $this->user->id();
        $this->service->updateById($id, $data);
        return $this->success();
    }

    #[Delete(
        path: '/admin/config/{id}',
        operationId: 'configDelete',
        summary: '删除配置',
        security: [['bearerAuth' => []]],
        tags: ['配置管理']
    )]
    #[Permission('config:delete')]
    #[ResultResponse(instance: new Result())]
    public function delete(string $id): Result
    {
        if (! $this->service->existsById($id)) {
            return $this->error(trans('config.not_found'));
        }
        $this->service->deleteById($id);
        return $this->success();
    }
}
