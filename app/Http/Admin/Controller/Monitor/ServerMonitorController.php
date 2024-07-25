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

use App\Service\Monitor\ServerMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

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
     */
    #[GetMapping('monitor'), Permission('system:monitor:server')]
    public function getServerInfo(): ResponseInterface
    {
        if (is_in_container()) {
            return $this->error(t('system.monitor_server_in_container'));
        }
        return $this->success([
            'cpu' => $this->service->getCpuInfo(),
            'memory' => $this->service->getMemInfo(),
            'phpenv' => $this->service->getPhpAndEnvInfo(),
            'disk' => $this->service->getDiskInfo(),
        ]);
    }
}
