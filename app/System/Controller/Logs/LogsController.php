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

namespace App\System\Controller\Logs;

use App\System\Service\SystemApiLogService;
use App\System\Service\SystemLoginLogService;
use App\System\Service\SystemOperLogService;
use App\System\Service\SystemQueueLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Middlewares\CheckModuleMiddleware;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 日志控制器
 * Class LogsController.
 */
#[Controller(prefix: 'system/logs'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
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
     * 获取登录日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getLoginLogPageList'), Permission('system:loginLog')]
    public function getLoginLogPageList(): ResponseInterface
    {
        return $this->success($this->loginLogService->getPageList($this->request->all()));
    }

    /**
     * 获取操作日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getOperLogPageList'), Permission('system:operLog')]
    public function getOperLogPageList(): ResponseInterface
    {
        return $this->success($this->operLogService->getPageList($this->request->all()));
    }

    /**
     * 获取接口日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getApiLogPageList'), Permission('system:apiLog')]
    public function getApiLogPageList(): ResponseInterface
    {
        return $this->success($this->apiLogService->getPageList($this->request->all()));
    }

    /**
     * 获取队列日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getQueueLogPageList'), Permission('system:queueLog')]
    public function getQueueLogPageList(): ResponseInterface
    {
        return $this->success($this->queueLogService->getPageList($this->request->all()));
    }

    /**
     * 删除队列日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deleteQueueLog'), Permission('system:queueLog:delete'), OperationLog]
    public function deleteQueueLog(): ResponseInterface
    {
        return $this->queueLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除操作日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deleteOperLog'), Permission('system:operLog:delete'), OperationLog]
    public function deleteOperLog(): ResponseInterface
    {
        return $this->operLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除登录日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deleteLoginLog'), Permission('system:loginLog:delete'), OperationLog]
    public function deleteLoginLog(): ResponseInterface
    {
        return $this->loginLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 删除API访问日志.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deleteApiLog'), Permission('system:apiLog:delete'), OperationLog]
    public function deleteApiLog(): ResponseInterface
    {
        return $this->apiLogService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }
}
