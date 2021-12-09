<?php

declare(strict_types=1);
namespace App\System\Controller\Api;

use App\System\Service\SystemApiColumnService;
use App\System\Request\Api\SystemApiColumnCreateRequest;
use App\System\Request\Api\SystemApiColumnUpdateRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineCollection;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 接口控制器
 * Class SystemApiColumnController
 * @Controller(prefix="system/apiColumn")
 * @Auth
 */
class SystemApiColumnController extends MineController
{
    /**
     * @Inject
     * @var SystemApiColumnService
     */
    protected $service;

    /**
     * 列表
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:apiColumn:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站列表
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:apiColumn:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增
     * @PostMapping("save")
     * @param SystemApiColumnCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:apiColumn:save")
     * @OperationLog
     */
    public function save(SystemApiColumnCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:apiColumn:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SystemApiColumnUpdateRequest $request
     * @return ResponseInterface
     * @Permission("system:apiColumn:update")
     * @OperationLog
     */
    public function update(int $id, SystemApiColumnUpdateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:apiColumn:delete")
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
     * @Permission("system:apiColumn:realDelete")
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
     * @Permission("system:apiColumn:recovery")
     * @OperationLog
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }

    /**
     * 字段导出
     * @PostMapping("export")
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Permission("system:apiColumn:export")
     * @return ResponseInterface
     */
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\System\Dto\ApiColumnDto::class, '字段列表');
    }

    /**
     * 字段导入
     * @PostMapping("import")
     * @Permission("system:apiColumn:import")
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import(): ResponseInterface
    {
        return $this->service->import(\App\System\Dto\ApiColumnDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @PostMapping("downloadTemplate")
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function downloadTemplate(): ResponseInterface
    {
        return (new MineCollection)->export(\App\System\Dto\ApiColumnDto::class, '模板下载', []);
    }
}