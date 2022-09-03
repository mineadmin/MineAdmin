<?php

declare(strict_types = 1);
namespace App\System\Controller\Monitor;

use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * 在线用户监控
 * Class OnlineUserMonitorController
 * @package App\System\Controller\Monitor
 */
#[Controller(prefix: "system/onlineUser"), Auth]
class OnlineUserMonitorController extends MineController
{
    #[Inject]
    protected SystemUserService $service;

    /**
     * 获取在线用户列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("system:onlineUser, system:onlineUser:index")]
    public function getPageList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getOnlineUserPageList($this->request->all()));
    }

    /**
     * 强退用户
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[PostMapping("kick"), Permission("system:onlineUser:kick")]
    public function kickUser(): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->kickUser((string) $this->request->input('id')) ?
            $this->success() : $this->error();
    }
}