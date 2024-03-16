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

namespace App\Setting\Controller\Settings;

use App\Setting\Request\SettingConfigGroupRequest;
use App\Setting\Service\SettingConfigGroupService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Annotation\RemoteState;
use Mine\Middlewares\CheckModuleMiddleware;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 系统配置组控制器
 * Class SystemConfigGroupController.
 */
#[Controller(prefix: 'setting/configGroup'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class SystemConfigGroupController extends MineController
{
    #[Inject]
    protected SettingConfigGroupService $service;

    /**
     * 获取系统组配置.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('setting:config, setting:config:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 保存配置组.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('setting:config:save'), OperationLog('保存配置组')]
    public function save(SettingConfigGroupRequest $request): ResponseInterface
    {
        return $this->service->save($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 更新配置组.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('update'), Permission('setting:config:update'), OperationLog('更新配置组')]
    public function update(SettingConfigGroupRequest $request): ResponseInterface
    {
        return $this->service->update((int) $this->request->input('id'), $request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 删除配置组.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('setting:config:delete'), OperationLog('删除配置组')]
    public function delete(): ResponseInterface
    {
        return $this->service->deleteConfigGroup((int) $this->request->input('id')) ? $this->success() : $this->error();
    }

    /**
     * 远程万能通用列表接口.
     */
    #[PostMapping('remote'), RemoteState(true)]
    public function remote(): ResponseInterface
    {
        return $this->success($this->service->getRemoteList($this->request->all()));
    }
}
