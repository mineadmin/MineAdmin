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

namespace App\System\Controller\App;

use App\System\Request\SystemAppRequest;
use App\System\Service\SystemAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
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
 * 应用管理控制器
 * Class SystemAppController.
 */
#[Controller(prefix: 'system/app'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class SystemAppController extends MineController
{
    #[Inject]
    protected SystemAppService $service;

    /**
     * 获取APP ID.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[GetMapping('getAppId')]
    public function getAppId(): ResponseInterface
    {
        return $this->success(['app_id' => $this->service->getAppId()]);
    }

    /**
     * 获取APP SECRET.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[GetMapping('getAppSecret')]
    public function getAppSecret(): ResponseInterface
    {
        return $this->success(['app_secret' => $this->service->getAppSecret()]);
    }

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('system:app, system:app:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 获取绑定接口列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getApiList')]
    public function getApiList(): ResponseInterface
    {
        return $this->success($this->service->getApiList((int) $this->request->input('id', null)));
    }

    /**
     * 回收站列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('system:app:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('system:app:save'), OperationLog]
    public function save(SystemAppRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('system:app:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('system:app:update'), OperationLog]
    public function update(int $id, SystemAppRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 绑定接口.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('bind/{id}'), Permission('system:app:bind'), OperationLog]
    public function bind(int $id): ResponseInterface
    {
        return $this->service->bind($id, $this->request->input('apiIds', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('system:app:delete')]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('system:app:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('system:app:recovery')]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('system:apiGroup:update'), OperationLog]
    public function changeStatus(SystemAppRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $this->request->input('id'), (string) $this->request->input('status'))
            ? $this->success() : $this->error();
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
