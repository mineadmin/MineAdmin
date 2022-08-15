<?php

declare(strict_types=1);
namespace App\System\Controller\Logs;

use App\System\Service\SystemApiLogService;
use App\System\Service\SystemLoginLogService;
use App\System\Service\SystemOperLogService;
use App\System\Service\SystemQueueLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * 日志控制器
 * Class LogsController
 * @package App\System\Controller\Logs
 */
#[Controller(prefix: "system/logs"), Auth]
class LogsController extends MineController
{
    /**
     * 登录日志服务
     */
    #[Inject]
    protected SystemLoginLogService $loginLogService;

    /**
     * 操作日志服务
     */
    #[Inject]
    protected SystemOperLogService $operLogService;

    /**
     * 接口日志服务
     */
    #[Inject]
    protected SystemApiLogService $apiLogService;

    /**
     * 队列日志服务
     */
    #[Inject]
    protected SystemQueueLogService $queueLogService;

    /**
     * 获取登录日志列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getLoginLogPageList"), Permission("system:loginLog")]
    public function getLoginLogPageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->loginLogService->getPageList($this->request->all()));
    }

    /**
     * 获取操作日志列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getOperLogPageList"), Permission("system:operLog")]
    public function getOperLogPageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->operLogService->getPageList($this->request->all()));
    }

    /**
     * 获取接口日志列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getApiLogPageList"), Permission("system:apiLog")]
    public function getApiLogPageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->apiLogService->getPageList($this->request->all()));
    }

    /**
     * 获取队列日志列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getQueueLogPageList"), Permission("system:queueLog")]
    public function getQueueLogPageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->queueLogService->getPageList($this->request->all()));
    }

    /**
     * 删除队列日志
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("deleteQueueLog"), Permission("system:queueLog:delete"), OperationLog]
    public function deleteQueueLog(): \Psr\Http\Message\ResponseInterface
    {
        return $this->queueLogService->delete((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除操作日志
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("deleteOperLog"), Permission("system:operLog:delete"), OperationLog]
    public function deleteOperLog(): \Psr\Http\Message\ResponseInterface
    {
        return $this->operLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除登录日志
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("deleteLoginLog"), Permission("system:loginLog:delete"), OperationLog]
    public function deleteLoginLog(): \Psr\Http\Message\ResponseInterface
    {
        return $this->loginLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除API访问日志
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("deleteApiLog"), Permission("system:apiLog:delete"), OperationLog]
    public function deleteApiLog(): \Psr\Http\Message\ResponseInterface
    {
        return $this->apiLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }
}
