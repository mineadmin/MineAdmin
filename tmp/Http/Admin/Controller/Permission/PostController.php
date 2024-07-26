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

use App\Http\Admin\Request\PostRequest;
use App\Service\Permission\PostService;
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
 * Class PostController.
 */
#[Controller(prefix: 'system/post'), Auth]
class PostController extends MineController
{
    #[Inject]
    protected PostService $service;

    /**
     * 岗位分页列表.
     */
    #[GetMapping('index'), Permission('system:post, system:post:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 岗位回收站分页列表.
     */
    #[GetMapping('recycle'), Permission('system:post:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 获取岗位列表.
     */
    #[GetMapping('list')]
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 保存数据.
     */
    #[PostMapping('save'), Permission('system:post:save'), OperationLog]
    public function save(PostRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 获取一条数据信息.
     */
    #[GetMapping('read/{id}'), Permission('system:post:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新数据.
     */
    #[PutMapping('update/{id}'), Permission('system:post:update'), OperationLog]
    public function update(int $id, PostRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站.
     */
    #[DeleteMapping('delete'), Permission('system:post:delete')]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     */
    #[DeleteMapping('realDelete'), Permission('system:post:realDelete'), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     */
    #[PutMapping('recovery'), Permission('system:post:recovery')]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改岗位状态
     */
    #[PutMapping('changeStatus'), Permission('system:post:changeStatus'), OperationLog]
    public function changeStatus(PostRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }

    /**
     * 数字运算操作.
     */
    #[PutMapping('numberOperation'), Permission('system:post:update'), OperationLog]
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
