<?php

declare(strict_types=1);
namespace App\System\Controller\App;

use App\System\Service\SystemAppService;
use App\System\Request\App\SystemAppCreateRequest;
use App\System\Request\App\SystemAppUpdateRequest;
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
 * 应用管理控制器
 * Class SystemAppController
 * @Controller(prefix="system/app")
 * @Auth
 */
class SystemAppController extends MineController
{
    /**
     * @Inject
     * @var SystemAppService
     */
    protected $service;

    /**
     * 获取APP ID
     * @GetMapping("getAppId")
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getAppId(): ResponseInterface
    {
        return $this->success(['app_id' => $this->service->getAppId()]);
    }

    /**
     * 获取APP SECRET
     * @GetMapping("getAppSecret")
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getAppSecret(): ResponseInterface
    {
        return $this->success(['app_secret' => $this->service->getAppSecret()]);
    }

    /**
     * 列表
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:app:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 获取绑定接口列表
     * @GetMapping("getApiList")
     * @return ResponseInterface
     */
    public function getApiList(): ResponseInterface
    {
        return $this->success($this->service->getApiList((int) $this->request->input('id', null)));
    }

    /**
     * 回收站列表
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:app:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增
     * @PostMapping("save")
     * @param SystemAppCreateRequest $request
     * @return ResponseInterface
     * @Permission("system:app:save")
     * @OperationLog
     */
    public function save(SystemAppCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:app:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SystemAppUpdateRequest $request
     * @return ResponseInterface
     * @Permission("system:app:update")
     * @OperationLog
     */
    public function update(int $id, SystemAppUpdateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 绑定接口
     * @PutMapping("bind/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("system:app:bind")
     * @OperationLog
     */
    public function bind(int $id): ResponseInterface
    {
        return $this->service->bind($id, $this->request->input('apiIds', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:app:delete")
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
     * @Permission("system:app:realDelete")
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
     * @Permission("system:app:recovery")
     * @OperationLog
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }
}