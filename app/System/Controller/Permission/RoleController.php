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

namespace App\System\Controller\Permission;

use App\System\Request\SystemRoleRequest;
use App\System\Service\SystemRoleService;
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
 * Class RoleController.
 */
#[Controller(prefix: 'system/role'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class RoleController extends MineController
{
    #[Inject]
    protected SystemRoleService $service;

    /**
     * 角色分页列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('system:role, system:role:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站角色分页列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('system:role:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 通过角色获取菜单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getMenuByRole/{id}')]
    public function getMenuByRole(int $id): ResponseInterface
    {
        return $this->success($this->service->getMenuByRole($id));
    }

    /**
     * 通过角色获取部门.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getDeptByRole/{id}')]
    public function getDeptByRole(int $id): ResponseInterface
    {
        return $this->success($this->service->getDeptByRole($id));
    }

    /**
     * 获取角色列表 (不验证权限).
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('list')]
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 新增角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('system:role:save'), OperationLog]
    public function save(SystemRoleRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('system:role:update'), OperationLog]
    public function update(int $id, SystemRoleRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 更新用户菜单权限.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('menuPermission/{id}'), Permission('system:role:menuPermission'), OperationLog]
    public function menuPermission(int $id): ResponseInterface
    {
        return $this->service->update($id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 更新用户数据权限.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('dataPermission/{id}'), Permission('system:role:dataPermission'), OperationLog]
    public function dataPermission(int $id): ResponseInterface
    {
        return $this->service->update($id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('system:role:delete')]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('system:role:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('system:role:recovery')]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改角色状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('system:role:changeStatus'), OperationLog]
    public function changeStatus(SystemRoleRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('system:role:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->success() : $this->error();
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
