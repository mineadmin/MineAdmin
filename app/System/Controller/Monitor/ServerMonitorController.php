<?php

declare(strict_types = 1);
namespace App\System\Controller\Monitor;


use App\System\Service\ServerMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;
use Swoole\Coroutine\Channel;

/**
 * Class ServerMonitorController
 * @package App\System\Controller\Monitor
 */
#[Controller(prefix: "system/server"), Auth]
class ServerMonitorController extends MineController
{
    #[Inject]
    protected ServerMonitorService $service;

    /**
     * 获取服务器信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("monitor"), Permission("system:monitor:server")]
    public function getServerInfo(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success([
            'cpu' => $this->service->getCpuInfo(),
            'memory' => $this->service->getMemInfo(),
            'phpenv' => $this->service->getPhpAndEnvInfo(),
            'disk'   => $this->service->getDiskInfo()
        ]);
    }
}