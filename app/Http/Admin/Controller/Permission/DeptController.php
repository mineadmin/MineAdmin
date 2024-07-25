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

use App\Http\Admin\Request\DeptRequest;
use App\Service\Permission\DeptService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Annotation\RemoteState;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeptController.
 */
#[Controller(prefix: 'system/dept'), Auth]
class DeptController extends MineController
{
    #[Inject]
    protected DeptService $service;

    /**
     * 部门树列表.
     */
    #[GetMapping('index'), Permission('system:dept, system:dept:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getTreeList($this->request->all()));
    }

    /**
     * 回收站部门树列表.
     */
    #[GetMapping('recycle'), Permission('system:dept:recycle')]
    public function recycleTree(): ResponseInterface
    {
        return $this->success($this->service->getTreeListByRecycle($this->request->all()));
    }

    /**
     * 前端选择树（不需要权限）.
     */
    #[GetMapping('tree')]
    public function tree(): ResponseInterface
    {
        return $this->success($this->service->getSelectTree());
    }

    #[GetMapping('getLeaderList'), Permission('system:dept, system:dept:index')]
    public function getLeaderList()
    {
        return $this->success($this->service->getLeaderList($this->request->all()));
    }

    /**
     * 新增部门.
     */
    #[PostMapping('save'), Permission('system:dept:save'), OperationLog]
    public function save(DeptRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 新增部门领导
     */
    #[PostMapping('addLeader'), Permission('system:dept:update'), OperationLog('新增部门领导')]
    public function addLeader(DeptRequest $request): ResponseInterface
    {
        return $this->service->addLeader($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 删除部门领导
     */
    #[DeleteMapping('delLeader'), Permission('system:dept:delete'), OperationLog('删除部门领导')]
    public function delLeader(DeptRequest $request): ResponseInterface
    {
        return $this->service->delLeader($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 更新部门.
     */
    #[PutMapping('update/{id}'), Permission('system:dept:update'), OperationLog]
    public function update(int $id, DeptRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除部门到回收站.
     */
    #[DeleteMapping('delete'), Permission('system:dept:delete')]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除部门 （清空回收站）.
     */
    #[DeleteMapping('realDelete'), Permission('system:dept:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        $data = $this->service->realDel((array) $this->request->input('ids', []));
        return is_null($data) ?
            $this->success() :
            $this->success(t('system.exists_children_ctu', ['names' => implode(',', $data)]));
    }

    /**
     * 单个或批量恢复在回收站的部门.
     */
    #[PutMapping('recovery'), Permission('system:dept:recovery')]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改部门状态
     */
    #[PutMapping('changeStatus'), Permission('system:dept:changeStatus'), OperationLog]
    public function changeStatus(DeptRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }

    /**
     * 数字运算操作.
     */
    #[PutMapping('numberOperation'), Permission('system:dept:update'), OperationLog]
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
