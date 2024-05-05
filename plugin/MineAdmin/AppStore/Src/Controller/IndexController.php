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

namespace Plugin\MineAdmin\AppStore\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;
use Xmo\AppStore\Service\Impl\AppStoreServiceImpl;

#[Controller(prefix: 'plugin/store')]
class IndexController extends MineController
{
    #[Inject]
    public AppStoreServiceImpl $serviceImpl;

    #[GetMapping('index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->serviceImpl->list($this->request->all()));
    }

    #[GetMapping('detail')]
    public function detail(): ResponseInterface
    {
        return $this->success($this->serviceImpl->view($this->request->input('identifier')));
    }

    #[GetMapping('hasAccessToken')]
    public function hasAccessToken(): ResponseInterface
    {
        return $this->success(['isHas' => ! is_null(env('MINE_ACCESS_TOKEN'))]);
    }
}
