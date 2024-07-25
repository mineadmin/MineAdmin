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

use App\Service\Permission\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 在线用户监控
 * Class OnlineUserMonitorController.
 */
#[Controller(prefix: 'system/onlineUser'), Auth]
class OnlineUserMonitorController extends MineController
{
    #[Inject]
    protected UserService $service;

    /**
     * 获取在线用户列表.
     */
    #[GetMapping('index'), Permission('system:onlineUser, system:onlineUser:index')]
    public function getPageList(): ResponseInterface
    {
        return $this->success($this->service->getOnlineUserPageList($this->request->all()));
    }

    /**
     * 强退用户.
     */
    #[PostMapping('kick'), Permission('system:onlineUser:kick')]
    public function kickUser(): ResponseInterface
    {
        return $this->service->kickUser((string) $this->request->input('id')) ?
            $this->success() : $this->error();
    }
}
