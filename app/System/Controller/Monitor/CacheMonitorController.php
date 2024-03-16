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

namespace App\System\Controller\Monitor;

use App\System\Service\CacheMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Middlewares\CheckModuleMiddleware;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 缓存监控
 * Class CacheMonitorController.
 */
#[Controller(prefix: 'system/cache'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class CacheMonitorController extends MineController
{
    #[Inject]
    protected CacheMonitorService $service;

    /**
     * 获取Redis服务器信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('monitor'), Permission('system:cache, system:cache:monitor')]
    public function getCacheInfo(): ResponseInterface
    {
        return $this->success($this->service->getCacheServerInfo());
    }

    /**
     * 查看key内容.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('view')]
    public function view(): ResponseInterface
    {
        return $this->success(['content' => $this->service->view($this->request->input('key'))]);
    }

    /**
     * 删除一个缓存.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('system:cache:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete($this->request->input('key', null))
            ? $this->success()
            : $this->error();
    }

    /**
     * 清空所有缓存.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('clear'), Permission('system:cache:clear'), OperationLog]
    public function clear(): ResponseInterface
    {
        return $this->service->clear() ? $this->success() : $this->error();
    }
}
