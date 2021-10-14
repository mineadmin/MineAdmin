<?php

declare(strict_types=1);
namespace App\Setting\Controller\Tools;

use App\Setting\Request\Tool\SettingCrontabCreateRequest;
use App\Setting\Service\SettingCrontabLogService;
use App\Setting\Service\SettingCrontabService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 定时任务控制器
 * Class CrontabController
 * @package App\Setting\Controller\Tools
 * @Controller(prefix="setting/crontab")
 */
class CrontabController extends MineController
{
    /**
     * @Inject
     * @var SettingCrontabService
     */
    protected $service;

    /**
     * @Inject
     * @var SettingCrontabLogService
     */
    protected $logService;

    /**
     * 获取列表分页数据
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("setting:crontab:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }

    /**
     * 获取日志列表分页数据
     * @GetMapping("logPageList")
     * @return ResponseInterface
     */
    public function logPageList(): ResponseInterface
    {
        return $this->success($this->logService->getPageList($this->request->all()));
    }

    /**
     * 保存数据
     * @PostMapping("save")
     * @param SettingCrontabCreateRequest $request
     * @return ResponseInterface
     * @Permission("setting:crontab:save")
     * @OperationLog
     */
    public function save(SettingCrontabCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 立即执行定时任务
     * @PostMapping("run")
     * @Permission("setting:crontab:run")
     * @OperationLog
     */
    public function run(): ResponseInterface
    {
        $id = $this->request->input('id', null);
        if (is_null($id)) {
            return $this->error();
        } else {
            return $this->service->run($id) ? $this->success() : $this->error();
        }
    }

    /**
     * 获取一条数据信息
     * @GetMapping("read/{id}")
     * @param int $id
     * @return ResponseInterface
     * @Permission("setting:crontab:read")
     */
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新数据
     * @PutMapping("update/{id}")
     * @param int $id
     * @param SettingCrontabCreateRequest $request
     * @return ResponseInterface
     * @Permission("setting:crontab:update")
     * @OperationLog
     */
    public function update(int $id, SettingCrontabCreateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("setting:crontab:delete")
     * @OperationLog
     */
    public function delete(String $ids): ResponseInterface
    {
        return $this->service->delete($ids) ? $this->success() : $this->error();
    }
}