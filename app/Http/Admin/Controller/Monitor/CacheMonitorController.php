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

namespace App\Http\Admin\Controller\Monitor;

use App\Service\Monitor\CacheMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 缓存监控
 * Class CacheMonitorController.
 */
#[Controller(prefix: 'system/cache'), Auth]
class CacheMonitorController extends MineController
{
    #[Inject]
    protected CacheMonitorService $service;

    /**
     * 获取Redis服务器信息.
     */
    #[GetMapping('monitor'), Permission('system:cache, system:cache:monitor')]
    public function getCacheInfo(): ResponseInterface
    {
        return $this->success($this->service->getCacheServerInfo());
    }

    /**
     * 查看key内容.
     */
    #[PostMapping('view')]
    public function view(): ResponseInterface
    {
        return $this->success(['content' => $this->service->view($this->request->input('key'))]);
    }

    /**
     * 删除一个缓存.
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
     */
    #[DeleteMapping('clear'), Permission('system:cache:clear'), OperationLog]
    public function clear(): ResponseInterface
    {
        return $this->service->clear() ? $this->success() : $this->error();
    }
}
