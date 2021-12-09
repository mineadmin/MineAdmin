<?php

declare(strict_types=1);
namespace App\System\Controller\App;

use App\System\Service\SystemAppGroupService;
use App\System\Request\App\SystemAppGroupCreateRequest;
use App\System\Request\App\SystemAppGroupUpdateRequest;
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
 * 应用分组控制器
 * Class SystemAppGroupController
 * @Controller(prefix="system/appGroup")
 * @Auth
 */
class SystemAppGroupController extends MineController
{
    /**
     * @Inject
     * @var SystemAppGroupService
     */
    protected $service;

    /**
     * 列表
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:appGroup:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 列表，无分页
     * @GetMapping("list")
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList());
    }

    /**
     * 回收站列表
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:appGroup:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增
     * @PostMapping("save")
     * @param SystemAppGroupCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:appGroup:save")
     * @OperationLog
     */
    public function save(SystemAppGroupCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:appGroup:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SystemAppGroupUpdateRequest $request
     * @return ResponseInterface
     * @Permission("system:appGroup:update")
     * @OperationLog
     */
    public function update(int $id, SystemAppGroupUpdateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:appGroup:delete")
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
     * @Permission("system:appGroup:realDelete")
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
     * @Permission("system:appGroup:recovery")
     * @OperationLog
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }
}