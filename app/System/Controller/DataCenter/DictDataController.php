<?php

declare(strict_types=1);
namespace App\System\Controller\DataCenter;

use App\System\Request\DictData\DictDataCreateRequest;
use App\System\Service\SystemDictDataService;
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
 * 字典类型控制器
 * Class LogsController
 * @package App\System\Controller\DataCenter
 * @Controller(prefix="system/dataDict")
 * @Auth
 */
class DictDataController extends MineController
{
    /**
     * 字典数据服务
     * @Inject
     * @var SystemDictDataService
     */
    protected $service;

    /**
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:dataDict:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * @GetMapping("list")
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }

    /**
     * @PostMapping("clearCache")
     * @Permission("system:dataDict:clearCache")
     * @OperationLog
     * @return ResponseInterface
     */
    public function clearCache(): ResponseInterface
    {
        return $this->service->clearCache() ? $this->success() : $this->error();
    }

    /**
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:dataDict:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增字典类型
     * @PostMapping("save")
     * @param DictDataCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:dataDict:save")
     * @OperationLog
     */
    public function save(DictDataCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 获取一个字典类型数据
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:dataDict:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新一个字典类型
     * @PutMapping("update/{id}")
     * @param int $id
     * @param DictDataCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:dataDict:update")
     */
    public function update(int $id, DictDataCreateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量字典数据
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:dataDict:delete")
     */
    public function delete(String $ids): ResponseInterface
    {
        return $this->service->delete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除用户 （清空回收站）
     * @DeleteMapping("realDelete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:dataDict:realDelete")
     * @OperationLog
     */
    public function realDelete(String $ids): ResponseInterface
    {
        return $this->service->realDelete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的用户
     * @PutMapping("recovery/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:dataDict:recovery")
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }
}
