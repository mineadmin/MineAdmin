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
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\MineController;
use Plugin\MineAdmin\AppStore\Service\Service;
use Psr\Http\Message\ResponseInterface;
use Mine\AppStore\Service\Impl\AppStoreServiceImpl;

#[Controller(prefix: 'plugin/store')]
class IndexController extends MineController
{
    #[Inject]
    public AppStoreServiceImpl $serviceImpl;

    #[Inject]
    public Service $Service;

    #[GetMapping('index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->serviceImpl->list($this->request->all()));
    }

    #[GetMapping('getMyApp')]
    public function getMyApp(): ResponseInterface
    {
        return $this->success($this->serviceImpl->myApp($this->request->all()));
    }

    #[GetMapping('getPayApp')]
    public function getPayApp(): ResponseInterface
    {
        return $this->success($this->serviceImpl->payApp());
    }

    #[GetMapping('detail')]
    public function detail(): ResponseInterface
    {
        return $this->success($this->serviceImpl->view($this->request->input('identifier')));
    }

    #[PostMapping('downloadAndInstall')]
    public function downloadAndInstall(): ResponseInterface
    {
        return $this->success($this->Service->downloadAndInstall($this->request->all()));
    }

    #[GetMapping('hasAccessToken')]
    public function hasAccessToken(): ResponseInterface
    {
        return $this->success(['isHas' => ! is_null(env('MINE_ACCESS_TOKEN'))]);
    }
}
