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

use App\System\Service\ServerMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * Class ServerMonitorController.
 */
#[Controller(prefix: 'system/server'), Auth]
class ServerMonitorController extends MineController
{
    #[Inject]
    protected ServerMonitorService $service;

    /**
     * 获取服务器信息.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('monitor'), Permission('system:monitor:server')]
    public function getServerInfo(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success([
            'cpu' => $this->service->getCpuInfo(),
            'memory' => $this->service->getMemInfo(),
            'phpenv' => $this->service->getPhpAndEnvInfo(),
            'disk' => $this->service->getDiskInfo(),
        ]);
    }
}
