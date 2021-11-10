<?php

declare(strict_types = 1);
namespace App\System\Controller\Permission;

use App\System\Request\Role\SystemRoleStatusRequest;
use App\System\Request\Role\SystemRoleCreateRequest;
use App\System\Service\SystemRoleService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Mine\Traits\ControllerTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RoleController
 * @package App\System\Controller
 * @Controller(prefix="system/role")
 * @Auth
 */
class RoleController extends MineController
{
    /**
     * @Inject
     * @var SystemRoleService
     */
    protected $service;

    /**
     * 获取角色分页列表
     * @GetMapping("index")
     * @Permission("system:role:index")
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:role:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 通过角色获取菜单
     * @GetMapping("getMenuByRole/{id}")
     * @param int $id
     * @return ResponseInterface
     */
    public function getMenuByRole(int $id): ResponseInterface
    {
        return $this->success($this->service->getMenuByRole($id));
    }

    /**
     * 通过角色获取部门
     * @GetMapping("getDeptByRole/{id}")
     * @param int $id
     * @return ResponseInterface
     */
    public function getDeptByRole(int $id): ResponseInterface
    {
        return $this->success($this->service->getDeptByRole($id));
    }

    /**
     * 获取角色列表
     * @GetMapping("list")
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 新增角色
     * @PostMapping("save")
     * @param SystemRoleCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:role:save")
     * @OperationLog
     */
    public function save(SystemRoleCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新角色
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SystemRoleCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:role:update")
     * @OperationLog
     */
    public function update(int $id, SystemRoleCreateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 更新用户菜单权限
     * @PutMapping("menuPermission/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:role:menuPermission")
     * @OperationLog
     */
    public function menuPermission(int $id): ResponseInterface
    {
        return $this->service->update($id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 更新用户数据权限
     * @PutMapping("dataPermission/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:role:dataPermission")
     * @OperationLog
     */
    public function dataPermission(int $id): ResponseInterface
    {
        return $this->service->update($id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:role:delete")
     * @OperationLog
     */
    public function delete(String $ids): ResponseInterface
    {
        return $this->service->delete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）
     * @DeleteMapping("realDelete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:role:realDelete")
     * @OperationLog
     */
    public function realDelete(String $ids): ResponseInterface
    {
        return $this->service->realDelete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据
     * @PutMapping("recovery/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:role:recovery")
     * @OperationLog
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }

    /**
     * 更改角色状态
     * @PutMapping("changeStatus")
     * @param SystemRoleStatusRequest $request
     * @return ResponseInterface
     * @Permission("system:role:changeStatus")
     * @OperationLog
     */
    public function changeStatus(SystemRoleStatusRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }
}