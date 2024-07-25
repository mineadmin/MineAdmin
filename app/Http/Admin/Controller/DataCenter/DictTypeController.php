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

namespace App\Http\Admin\Controller\DataCenter;

use App\Http\Admin\Request\DictTypeRequest;
use App\Service\DataCenter\DictTypeService;
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
 * 字典类型控制器
 * Class LogsController.
 */
#[Controller(prefix: 'system/dictType'), Auth]
class DictTypeController extends MineController
{
    #[Inject]
    protected DictTypeService $service;

    /**
     * 获取字典列表（分页）.
     */
    #[GetMapping('index'), Permission('system:dict, system:dict:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 获取字典列表（无分页）.
     */
    #[GetMapping('list'), Permission('system:dict, system:dict:list')]
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }

    /**
     * 回收站列表.
     */
    #[GetMapping('recycle'), Permission('system:dict:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增字典类型.
     */
    #[PostMapping('save'), Permission('system:dict:save'), OperationLog('新增字典类型')]
    public function save(DictTypeRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 获取一个字典类型数据.
     */
    #[GetMapping('read/{id}'), Permission('system:dict:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新一个字典类型.
     */
    #[PutMapping('update/{id}'), Permission('system:dict:update'), OperationLog('更新字典类型')]
    public function update(int $id, DictTypeRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量字典数据.
     */
    #[DeleteMapping('delete'), Permission('system:dict:delete')]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除字典数据 （清空回收站）.
     */
    #[DeleteMapping('realDelete'), Permission('system:dict:realDelete'), OperationLog('删除字典类型')]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的用户.
     */
    #[PutMapping('recovery'), Permission('system:dict:recovery')]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改字典类型状态
     */
    #[PutMapping('changeStatus'), Permission('system:dict:update'), OperationLog('修改字典类型状态')]
    public function changeStatus(DictTypeRequest $request): ResponseInterface
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
