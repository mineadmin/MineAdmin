<?php

declare(strict_types = 1);
namespace App\System\Controller\Permission;

use App\System\Request\SystemDeptRequest;
use App\System\Service\SystemDeptService;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeptController
 * @package App\System\Controller
 */
#[Controller(prefix: "system/dept"), Auth]
class DeptController extends MineController
{
    #[Inject]
    protected SystemDeptService $service;

    /**
     * 部门树列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("system:dept, system:dept:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getTreeList($this->request->all()));
    }

    /**
     * 回收站部门树列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("recycle"), Permission("system:dept:recycle")]
    public function recycleTree():ResponseInterface
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
        return $this->success($this->service->getSelectTree());
    }

    #[GetMapping("getLeaderList"), Permission("system:dept, system:dept:index")]
    public function getLeaderList()
    {
        return $this->success($this->service->getLeaderList($this->request->all()));
    }

    /**
     * 新增部门
     * @param SystemDeptRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("system:dept:save"), OperationLog]
    public function save(SystemDeptRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 新增部门领导
     * @param SystemDeptRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("addLeader"), Permission("system:dept:update"), OperationLog("新增部门领导")]
    public function addLeader(SystemDeptRequest $request): ResponseInterface
    {
        return $this->service->addLeader($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 删除部门领导
     * @param SystemDeptRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("delLeader"), Permission("system:dept:delete"), OperationLog("删除部门领导")]
    public function delLeader(): ResponseInterface
    {
        return $this->service->delLeader($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 更新部门
     * @param int $id
     * @param SystemDeptRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("system:dept:update"), OperationLog]
    public function update(int $id, SystemDeptRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除部门到回收站
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("system:dept:delete")]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除部门 （清空回收站）
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("realDelete"), Permission("system:dept:realDelete"), OperationLog]
    public function realDelete(): ResponseInterface
    {
        $data = $this->service->realDel((array) $this->request->input('ids', []));
        return is_null($data) ?
            $this->success() :
            $this->success(t('system.exists_children_ctu', ['names' => implode(',', $data)]));
    }

    /**
     * 单个或批量恢复在回收站的部门
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("recovery"), Permission("system:dept:recovery")]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改部门状态
     * @param SystemDeptRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("changeStatus"), Permission("system:dept:changeStatus"), OperationLog]
    public function changeStatus(SystemDeptRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }

    /**
     * 数字运算操作
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("numberOperation"), Permission("system:dept:update"), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->success() : $this->error();
    }
}