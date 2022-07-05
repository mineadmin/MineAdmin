<?php
declare(strict_types = 1);
namespace App\System\Controller\Permission;

use App\System\Request\Menu\SystemMenuCreateRequest;
use App\System\Service\SystemMenuService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Helper\LoginUser;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MenuController
 * @package App\System\Controller
 */
#[Controller(prefix: "system/menu"), Auth]
class MenuController extends MineController
{
    #[Inject]
    protected SystemMenuService $service;

    /**
     * 菜单树列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("system:menu:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getTreeList($this->request->all()));
    }

    /**
     * 回收站菜单树列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("recycle"), Permission("system:menu:recycle")]
    public function recycle():ResponseInterface
    {
        return $this->success($this->service->getTreeListByRecycle($this->request->all()));
    }

    /**
     * 前端选择树（不需要权限）
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("tree")]
    public function tree(): ResponseInterface
    {
        return $this->success($this->service->getSelectTree($this->request->all()));
    }

    /**
     * 新增菜单
     * @param SystemMenuCreateRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("system:menu:save"), OperationLog]
    public function save(SystemMenuCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新菜单
     * @param int $id
     * @param SystemMenuCreateRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("system:menu:update"), OperationLog]
    public function update(int $id, SystemMenuCreateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all())
            ? $this->success() : $this->error(t('mineadmin.data_no_change'));
    }

    /**
     * 单个或批量删除菜单到回收站
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("system:menu:delete")]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除菜单 （清空回收站）
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("realDelete"), Permission("system:menu:realDelete"), OperationLog]
    public function realDelete(): ResponseInterface
    {
        $menus = $this->service->realDel((array) $this->request->input('ids', []));
        return is_null($menus) ? 
        $this->success() :
        $this->success(t('system.exists_children_ctu', ['names' => implode(',', $menus)]));
    }

    /**
     * 单个或批量恢复在回收站的菜单
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("recovery"), Permission("system:menu:recovery")]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }
}