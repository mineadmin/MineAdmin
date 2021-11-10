<?php

declare(strict_types = 1);
namespace App\System\Controller\Permission;

use App\System\Request\Post\SystemPostCreateRequest;
use App\System\Request\Post\SystemPostStatusRequest;
use App\System\Service\SystemPostService;
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
use Psr\Http\Message\ResponseInterface;

/**
 * Class PostController
 * @package App\System\Controller
 * @Controller(prefix="system/post")
 * @Auth
 */
class PostController extends MineController
{
    /**
     * @Inject
     * @var SystemPostService
     */
    protected $service;

    /**
     * 获取列表分页数据
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:post:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:post:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 获取列表数据
     * @GetMapping("list")
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 保存数据
     * @PostMapping("save")
     * @param SystemPostCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:post:save")
     * @OperationLog
     */
    public function save(SystemPostCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 获取一条数据信息
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:post:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新数据
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SystemPostCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:post:update")
     */
    public function update(int $id, SystemPostCreateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:post:delete")
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
     * @Permission("system:post:realDelete")
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
     * @Permission("system:post:recovery")
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }

    /**
     * 更改岗位状态
     * @PutMapping("changeStatus")
     * @param SystemPostStatusRequest $request
     * @return ResponseInterface
     * @Permission("system:post:changeStatus")
     * @OperationLog
     */
    public function changeStatus(SystemPostStatusRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }
}