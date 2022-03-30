<?php

declare(strict_types=1);
namespace App\System\Controller\Monitor;

use App\System\Service\RelyMonitorService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * Class RelyMonitorController
 * @package App\System\Controller\Monitor
 */
#[Controller(prefix: "system/rely"), Auth]
class RelyMonitorController extends MineController
{
    #[Inject]
    protected RelyMonitorService $service;

    /**
     * 获取依赖包列表数据
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("system:monitor:rely")]
    public function getPackagePageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getPackagePageList($this->request->all()));
    }

    /**
     * 获取依赖包详细信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("detail"), Permission("system:monitor:relyDetail")]
    public function getDetail(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getPackageDetail($this->request->input('name', '')));
    }
}